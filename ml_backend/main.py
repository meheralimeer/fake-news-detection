import joblib
import re
import numpy as np
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from lime.lime_text import LimeTextExplainer
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
import nltk

# Initialize FastAPI app
app = FastAPI(title="NewsGuard API", version="2.0")

# Global variables
model = None
vectorizer = None
stop_words = None
lemmatizer = None

# Request Models
class NewsRequest(BaseModel):
    text: str

# 1. LOAD ARTIFACTS ON STARTUP
@app.on_event("startup")
async def load_artifacts():
    global model, vectorizer, stop_words, lemmatizer
    try:
        print("Loading NewsGuard Artifacts...")
        model = joblib.load("newsguard_model.joblib")
        vectorizer = joblib.load("newsguard_vectorizer.joblib")
        
        # Ensure NLTK resources are available (they should be from training)
        try:
            stop_words = set(stopwords.words('english'))
            lemmatizer = WordNetLemmatizer()
        except LookupError:
            nltk.download('stopwords')
            nltk.download('wordnet')
            nltk.download('omw-1.4')
            stop_words = set(stopwords.words('english'))
            lemmatizer = WordNetLemmatizer()
            
        print("Artifacts loaded successfully!")
    except Exception as e:
        print(f"Error loading artifacts: {e}")

# 2. PREPROCESSING (Must match training)
def preprocess_text(text):
    text = str(text).lower()
    text = re.sub(r'https?://\S+|www\.\S+', '', text)
    text = re.sub(r'[^a-zA-Z\s]', '', text)
    words = text.split()
    # We need to handle the case where stop_words/lemmatizer might not be loaded if startup failed
    if stop_words and lemmatizer:
        words = [lemmatizer.lemmatize(word) for word in words if word not in stop_words]
    return " ".join(words)

# 3. PREDICTION ENDPOINT
@app.post("/predict")
async def predict_news(request: NewsRequest):
    if not model or not vectorizer:
        raise HTTPException(status_code=500, detail="Model not loaded")

    cleaned_text = preprocess_text(request.text)
    
    # Vectorize
    # transform expects an iterable, so wrap in list
    features = vectorizer.transform([cleaned_text])
    
    # Predict
    # 0 = Fake, 1 = Real (based on training script)
    prediction_class = model.predict(features)[0]
    prediction_prob = model.predict_proba(features)[0]
    
    label = "Real" if prediction_class == 1 else "Fake"
    confidence = prediction_prob[1] if prediction_class == 1 else prediction_prob[0]
    
    return {
        "status": "success",
        "prediction": label,
        "confidence_score": float(confidence),
        "raw_probability": float(prediction_prob[1]) # Prob of being Real
    }

# 4. EXPLAINABILITY ENDPOINT (LIME)
@app.post("/explain")
async def explain_news(request: NewsRequest):
    if not model or not vectorizer:
        raise HTTPException(status_code=500, detail="Model not loaded")

    # LIME Explainer
    explainer = LimeTextExplainer(class_names=['Fake', 'Real'])
    
    # Create a pipeline function for LIME
    # LIME passes raw text, we need to preprocess -> vectorize -> predict_proba
    def pipeline(texts):
        processed = [preprocess_text(t) for t in texts]
        features = vectorizer.transform(processed)
        return model.predict_proba(features)

    # Generate explanation
    # num_features=10: Top 10 words
    exp = explainer.explain_instance(request.text, pipeline, num_features=10)
    
    # Get list of (word, weight)
    explanation_list = exp.as_list()
    
    return {
        "status": "success",
        "explanation": explanation_list
    }

@app.get("/")
def home():
    return {"message": "NewsGuard API is Running!"}