# NewsGuard - AI-Powered Fake News Detection System

<div align="center">

**A full-stack fake news detection system with explainable AI, web dashboard, and browser extension**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel)](https://laravel.com)
[![FastAPI](https://img.shields.io/badge/FastAPI-Python-009688?logo=fastapi)](https://fastapi.tiangolo.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4-38B2AC?logo=tailwind-css)](https://tailwindcss.com)
[![Chrome Extension](https://img.shields.io/badge/Chrome-Extension-4285F4?logo=google-chrome)](https://developer.chrome.com/docs/extensions/)

</div>

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Architecture](#architecture)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Usage](#usage)
- [Browser Extension](#browser-extension)
- [Screenshots](#screenshots)
- [Model Details](#model-details)
- [API Documentation](#api-documentation)
- [Contributing](#contributing)

---

## 🌟 Overview

**NewsGuard** is a comprehensive fake news detection system that combines machine learning, web development, and browser automation to help users identify misinformation online. The system provides:

- **Web Dashboard**: Analyze news articles manually with detailed explanations
- **ML API**: FastAPI backend serving a Logistic Regression + TF-IDF model
- **Browser Extension**: Automatic detection and inline highlighting on any news website
- **Explainability**: LIME-based explanations showing which words influenced the prediction

---

## ✨ Features

### 🔍 Detection Capabilities
- ✅ Text-based fake news classification (Real vs Fake)
- ✅ URL-based analysis with automatic content extraction
- ✅ Confidence score for each prediction
- ✅ Explainability via LIME (Local Interpretable Model-agnostic Explanations)

### 🎨 User Interface
- ✅ Modern, responsive web dashboard (Light/Dark themes)
- ✅ Real-time loading indicators and progress tracking
- ✅ Prediction history with detailed logs
- ✅ Admin settings for dataset upload and model retraining

### 🧩 Browser Extension
- ✅ Chrome & Firefox support (Manifest v3)
- ✅ Automatic article text extraction
- ✅ Inline word highlighting (green = real, red = fake)
- ✅ Popup with instant results and web app integration

### ⚙️ Model Management
- ✅ Upload custom training datasets (CSV)
- ✅ Retrain model with configurable parameters
- ✅ Real-time training progress tracking
- ✅ Model status monitoring

---

## 🏗️ Architecture

```
┌─────────────────┐      ┌──────────────────┐      ┌─────────────────┐
│  Browser Ext    │◄────►│  Laravel Web App │◄────►│  FastAPI ML API │
│  (Chrome/FF)    │      │  (Dashboard)     │      │  (TF-IDF + LR)  │
└─────────────────┘      └──────────────────┘      └─────────────────┘
                                  │
                                  ▼
                         ┌────────────────┐
                         │   MySQL/SQLite │
                         │   (History DB) │
                         └────────────────┘
```

### Data Flow
1. User inputs text/URL or uses browser extension
2. Laravel receives request and forwards to ML API
3. FastAPI processes text, runs prediction & LIME explanation
4. Results return to Laravel, saved in database
5. Laravel displays results with visualizations
6. Browser extension highlights suspicious words inline

---

## 🛠️ Tech Stack

### Frontend
- **Framework**: Laravel Blade Templates
- **Styling**: TailwindCSS + Custom CSS
- **Interactivity**: Alpine.js
- **Browser Extension**: Manifest v3 (Chrome/Firefox)

### Backend
- **Web Framework**: Laravel 12 (PHP 8.2+)
- **ML API**: FastAPI (Python)
- **Database**: MySQL / SQLite
- **Queue System**: Laravel Queues (optional)

### Machine Learning
- **Model**: Logistic Regression
- **Vectorizer**: TF-IDF (unigrams + bigrams)
- **Explainability**: LIME (Local Interpretable Model-agnostic Explanations)
- **Libraries**: scikit-learn, joblib, NLTK

### DevOps
- **Package Management**: Composer (PHP), pip (Python), npm (JS)
- **Development**: Laravel Sail (Docker), Artisan, Uvicorn

---

## 📦 Installation

### Prerequisites
- PHP 8.2+
- Python 3.9+
- Composer
- Node.js & npm
- MySQL (or SQLite for development)

### 1. Clone Repository
```bash
git clone https://github.com/yourusername/fake-news-detection.git
cd fake-news-detection
```

### 2. Laravel Setup
```bash
cd fake-news-detection
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

### 3. Python ML Backend
```bash
cd ../ml_backend
pip install -r requirements.txt
```

**Note**: Ensure you have the trained model files:
- `newsguard_model.joblib`
- `newsguard_vectorizer.joblib`

### 4. Start Services
```bash
# Terminal 1: Laravel
cd fake-news-detection
php artisan serve

# Terminal 2: FastAPI ML Backend
cd ml_backend
uvicorn main:app --reload
```

### 5. Configure Environment
Update `.env` in the Laravel directory:
```env
ML_API_URL=http://localhost:8000
```

---

## 🚀 Usage

### Web Dashboard
1. Navigate to `http://localhost:8000`
2. Paste article text or enter a URL
3. Click "Analyze Authenticity"
4. View prediction, confidence, and explanation

### Settings Page
1. Go to `http://localhost:8000/settings`
2. **Upload Dataset**: Upload `Fake.csv` and `True.csv` files
3. **Retrain Model**: Configure parameters and click "Start Retraining"
4. Monitor training progress in real-time

### History
1. Go to `http://localhost:8000/history`
2. View all past predictions
3. Click any item to see detailed explanation

---

## 🧩 Browser Extension

### Installation
1. Open `chrome://extensions/` (Chrome) or `about:debugging#/runtime/this-firefox` (Firefox)
2. Enable "Developer mode"
3. Click "Load unpacked" (Chrome) or "Load Temporary Add-on" (Firefox)
4. Select the `browser-extension` folder

### Usage
1. Navigate to any news article
2. Click the NewsGuard extension icon
3. Click "Analyze This Article"
4. View results in popup
5. Click "Highlight on Page" to see inline word highlights
6. Click "View Explanation" to open detailed report in web app

See [`browser-extension/README.md`](browser-extension/README.md) for more details.

---

## 📸 Screenshots

![webapp_home_screenshot](./screenshots/image.png)

---

## 🤖 Model Details

### Training Data
- **Dataset**: Kaggle "Fake and real news dataset"
- **Classes**: Fake (0), Real (1)
- **Features**: TF-IDF vectors (10,000 features, unigrams + bigrams)

### Model Architecture
- **Algorithm**: Logistic Regression
- **Max Iterations**: 1,000 (configurable)
- **Preprocessing**: Lowercase, punctuation removal, stopword removal, lemmatization

### Explainability
- **Method**: LIME (Local Interpretable Model-agnostic Explanations)
- **Features**: Top 10 contributing words with weights
- **Visualization**: Word-level highlighting with positive/negative influence

### Performance
- **Accuracy**: ~95% (on test set)
- **Evaluation**: Classification report, confusion matrix

---

## 📡 API Documentation

### ML API Endpoints

#### `POST /predict`
Predict if text is fake or real.

**Request:**
```json
{
  "text": "Article content here..."
}
```

**Response:**
```json
{
  "status": "success",
  "prediction": "Real",
  "confidence": 0.92
}
```

#### `POST /explain`
Get LIME explanation for text.

**Request:**
```json
{
  "text": "Article content here..."
}
```

**Response:**
```json
{
  "status": "success",
  "explanation": [
    ["word1", 0.45],
    ["word2", -0.32]
  ]
}
```

#### `GET /status`
Get model and training status.

**Response:**
```json
{
  "model_loaded": true,
  "vectorizer_loaded": true,
  "model_type": "Logistic Regression + TF-IDF",
  "training_status": {
    "is_training": false,
    "progress": 0,
    "current_step": "Idle"
  }
}
```

### Laravel API Endpoints

#### `POST /api/extension/check`
Extension endpoint for article analysis.

**Request:**
```json
{
  "text": "Article content...",
  "url": "https://example.com/article",
  "title": "Article Title"
}
```

**Response:**
```json
{
  "success": true,
  "prediction": "Real",
  "confidence_score": 0.92,
  "explanation": [...],
  "history_id": 123
}
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👤 Author

**Meher Ali**
- GitHub: [@meheralimeer](https://github.com/meheralimeer)
- Email: meherali.meer@gmail.com

---

## 🙏 Acknowledgments

- [Kaggle Fake and real news dataset](https://www.kaggle.com/datasets/clmentbisaillon/fake-and-real-news-dataset)
- [LIME - Local Interpretable Model-agnostic Explanations](https://github.com/marcotcr/lime)
- Laravel, FastAPI, and TailwindCSS communities

---

<div align="center">
Made with ❤️ using Laravel, FastAPI, and TailwindCSS
</div>
