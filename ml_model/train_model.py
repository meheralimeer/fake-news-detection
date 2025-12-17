import pandas as pd
import numpy as np
import re
import nltk
import joblib
import os
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import accuracy_score, classification_report

# 1. Setup & Config
DATA_DIR = "ml_model/kaggle/input/fake-and-real-news-dataset"
OUTPUT_DIR = "ml_backend"
FAKE_CSV = os.path.join(DATA_DIR, "Fake.csv")
TRUE_CSV = os.path.join(DATA_DIR, "True.csv")

# Ensure output directory exists
os.makedirs(OUTPUT_DIR, exist_ok=True)

# Download NLTK resources
print("Downloading NLTK resources...")
nltk.download('stopwords')
nltk.download('wordnet')
nltk.download('omw-1.4')

# 2. Data Loading
print("Loading datasets...")
if not os.path.exists(FAKE_CSV) or not os.path.exists(TRUE_CSV):
    raise FileNotFoundError(f"Datasets not found in {DATA_DIR}. Please ensure Kaggle dataset is present.")

df_fake = pd.read_csv(FAKE_CSV)
df_true = pd.read_csv(TRUE_CSV)

# Add labels
df_fake['label'] = 0  # Fake
df_true['label'] = 1  # Real

# Combine
df = pd.concat([df_fake, df_true], axis=0).reset_index(drop=True)
print(f"Total samples: {len(df)}")

# Shuffle
df = df.sample(frac=1, random_state=42).reset_index(drop=True)

# 3. Preprocessing
print("Preprocessing text...")
stop_words = set(stopwords.words('english'))
lemmatizer = WordNetLemmatizer()

def preprocess_text(text):
    # Lowercase
    text = str(text).lower()
    # Remove URLs
    text = re.sub(r'https?://\S+|www\.\S+', '', text)
    # Remove punctuation and numbers
    text = re.sub(r'[^a-zA-Z\s]', '', text)
    # Tokenize & Remove stopwords & Lemmatize
    words = text.split()
    words = [lemmatizer.lemmatize(word) for word in words if word not in stop_words]
    return " ".join(words)

# Apply preprocessing (this might take a while)
# For speed in this demo, let's just use the 'text' column. 
# If 'text' is empty, fallback to 'title'.
df['content'] = df['text'].fillna('') + " " + df['title'].fillna('')
df['clean_text'] = df['content'].apply(preprocess_text)

# 4. Vectorization
print("Vectorizing...")
# Using unigrams and bigrams, limited to 10k features for performance/size balance
vectorizer = TfidfVectorizer(ngram_range=(1, 2), max_features=10000)
X = vectorizer.fit_transform(df['clean_text'])
y = df['label']

# 5. Split Data
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# 6. Model Training
print("Training Logistic Regression...")
model = LogisticRegression(max_iter=1000)
model.fit(X_train, y_train)

# 7. Evaluation
print("Evaluating...")
y_pred = model.predict(X_test)
accuracy = accuracy_score(y_test, y_pred)
print(f"Accuracy: {accuracy:.4f}")
print(classification_report(y_test, y_pred))

# 8. Save Artifacts
print("Saving model and vectorizer...")
joblib.dump(model, os.path.join(OUTPUT_DIR, "newsguard_model.joblib"))
joblib.dump(vectorizer, os.path.join(OUTPUT_DIR, "newsguard_vectorizer.joblib"))

print("Done! Artifacts saved to ml_backend/")
