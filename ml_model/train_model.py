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

def train(data_dir="ml_model/kaggle/input/fake-and-real-news-dataset", output_dir="ml_backend", max_features=10000, max_iter=1000, progress_callback=None):
    # 1. Setup & Config
    FAKE_CSV = os.path.join(data_dir, "Fake.csv")
    TRUE_CSV = os.path.join(data_dir, "True.csv")

    # Ensure output directory exists
    os.makedirs(output_dir, exist_ok=True)

    # Download NLTK resources
    print("Downloading NLTK resources...")
    try:
        nltk.data.find('corpora/stopwords')
        nltk.data.find('corpora/wordnet')
        nltk.data.find('corpora/omw-1.4')
    except LookupError:
        nltk.download('stopwords')
        nltk.download('wordnet')
        nltk.download('omw-1.4')

    # 2. Data Loading
    if progress_callback:
        progress_callback("Loading Data", 10, "Loading CSV files...")
    print("Loading datasets...")
    if not os.path.exists(FAKE_CSV) or not os.path.exists(TRUE_CSV):
        raise FileNotFoundError(f"Datasets not found in {data_dir}. Please ensure Kaggle dataset is present.")

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
    if progress_callback:
        progress_callback("Preprocessing", 30, "Cleaning and tokenizing text...")
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

    # Apply preprocessing
    df['content'] = df['text'].fillna('') + " " + df['title'].fillna('')
    df['clean_text'] = df['content'].apply(preprocess_text)

    # 4. Vectorization
    if progress_callback:
        progress_callback("Vectorizing", 50, "Converting text to TF-IDF features...")
    print("Vectorizing...")
    vectorizer = TfidfVectorizer(ngram_range=(1, 2), max_features=int(max_features))
    X = vectorizer.fit_transform(df['clean_text'])
    y = df['label']

    # 5. Split Data
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

    # 6. Model Training
    if progress_callback:
        progress_callback("Training", 70, "Training Logistic Regression model...")
    print("Training Logistic Regression...")
    model = LogisticRegression(max_iter=int(max_iter))
    model.fit(X_train, y_train)

    # 7. Evaluation
    if progress_callback:
        progress_callback("Evaluating", 90, "Testing model accuracy...")
    print("Evaluating...")
    y_pred = model.predict(X_test)
    accuracy = accuracy_score(y_test, y_pred)
    print(f"Accuracy: {accuracy:.4f}")
    report = classification_report(y_test, y_pred, output_dict=True)

    # 8. Save Artifacts
    if progress_callback:
        progress_callback("Saving", 95, "Saving model and vectorizer...")
    print("Saving model and vectorizer...")
    joblib.dump(model, os.path.join(output_dir, "newsguard_model.joblib"))
    joblib.dump(vectorizer, os.path.join(output_dir, "newsguard_vectorizer.joblib"))

    print("Done! Artifacts saved.")
    
    return {
        "accuracy": accuracy,
        "report": report
    }

if __name__ == "__main__":
    train()
