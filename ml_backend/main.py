import numpy as np
import re
import pickle
import tensorflow as tf
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from tensorflow.keras.preprocessing.sequence import pad_sequences

# Initialize FastAPI app
app = FastAPI(title="Fake News Detection API", version="1.0")

# Global variables to hold model and tokenizer
model = None
tokenizer = None

# CONSTANTS (Must match your training config)
MAX_LENGTH = 300
vocab_size = 20000 

# Define Request Body format
class NewsRequest(BaseModel):
    text: str


# 1. LOAD ARTIFACTS ON STARTUP
@app.on_event("startup")
async def load_model():
    global model, tokenizer
    try:
        print("Loading Model and Tokenizer...")
        
        # Load Keras Model
        model = tf.keras.models.load_model("fake_news_cnn.keras")
        
        # Load Tokenizer
        with open("tokenizer.pkl", "rb") as f:
            tokenizer = pickle.load(f)
            
        print("Model and Tokenizer loaded successfully!")
    except Exception as e:
        print(f"Error loading files: {e}")


# 2. PREPROCESSING FUNCTION (Exact copy from training)
def clean_text(text):
    text = str(text).lower()
    text = re.sub(r'https?://\S+|www\.\S+', '', text)  # URLs
    text = re.sub(r'[^a-zA-Z\s]', '', text)            # Punctuation/numbers
    text = re.sub(r'\s+', ' ', text).strip()
    return text


# 3. PREDICTION ENDPOINT
@app.post("/predict")
async def predict_news(request: NewsRequest):
    if not model or not tokenizer:
        raise HTTPException(status_code=500, detail="Model not loaded")

    # A. Clean the input text
    cleaned_text = clean_text(request.text)

    # B. Tokenize
    # Note: texts_to_sequences expects a list, so we wrap text in []
    seq = tokenizer.texts_to_sequences([cleaned_text])

    # C. Pad Sequence
    padded = pad_sequences(seq, maxlen=MAX_LENGTH)

    # D. Predict
    # Result is a probability between 0 and 1 (Sigmoid)
    prediction_prob = model.predict(padded)[0][0]
    
    # E. Interpret Result
    # Your labels: 0 = Fake, 1 = True
    label = "Real" if prediction_prob > 0.5 else "Fake"
    
    # Calculate confidence percentage
    # If prob is 0.1 (Fake), confidence is 0.9 (90% Fake)
    confidence = prediction_prob if prediction_prob > 0.5 else 1 - prediction_prob

    return {
        "status": "success",
        "input_preview": request.text[:50] + "...",
        "prediction": label,
        "confidence_score": float(confidence),  # e.g., 0.95
        "raw_probability": float(prediction_prob)
    }


# 4. ROOT ENDPOINT (Health Check)
@app.get("/")
def home():
    return {"message": "Fake News Detection API is Running!"}