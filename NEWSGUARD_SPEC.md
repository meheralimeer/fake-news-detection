# Final Refined Project Idea — Executive Summary

## Project Title
**NewsGuard: Web + Extension Fake-News Detector with Explainability**

## One-line Summary
A full-stack system that detects whether an online news article is likely fake or real using a supervised text-classification model (TF-IDF + classifier) exposed via a Python ML API, integrated into a Laravel web app (dashboard, logs, dataset/model management), and a browser extension that automatically extracts page content, requests a prediction, and highlights suspicious parts — with explainability implemented via LIME (detailed explanations in dashboard) and TF-IDF highlighting (fast inline highlights in the extension).

## Key Goals
1.  **Build an accurate, explainable ML model** to classify news text as Fake or Real.
2.  **Provide a polished Laravel web interface** for manual checking, admin tasks, logging, dataset management, and model retraining.
3.  **Provide a browser extension (Chrome + Firefox)** that auto-detects article body, sends it to the web app, receives results, and highlights suspicious sentences/words inline.
4.  **Ensure explainability** so users and instructors can see why a prediction was made (LIME for per-article explanations; TF-IDF word highlights in extension).

## Core Components (high level)

### Python ML API (FastAPI or Flask)
-   **Endpoints**: `/predict`, `/explain`, `/health`, `/retrain` (admin only)
-   Loads trained model + TF-IDF vectorizer
-   Returns JSON: `{prediction, confidence, explanation_tokens, feature_importances}`

### Laravel Web App (PHP)
-   **User UI** (Bootstrap + Tailwind) to paste/check articles, view results.
-   **Admin dashboard**: logs, dataset upload, trigger retrain, model versions.
-   Acts as central server: extension → Laravel → Python API → Laravel → extension.
-   Stores logs and metadata in DB (MySQL/SQLite).

### Browser Extension (Manifest v3)
-   Content script extracts article text (auto: DOM selectors with fallbacks).
-   Sends text to Laravel endpoint (authentication token).
-   Receives prediction + TF-IDF highlights + optionally per-sentence scores.
-   Highlights suspicious phrases inline and shows popup with LIME link to full explanation in the web app.

## Explainability
-   **LIME on the Python API**: generate local explanations (top contributing tokens, positively and negatively). Shown on the web app in readable form (bar chart, words list).
-   **TF-IDF token weights** used to quickly highlight important words/phrases inside the browser extension for immediate UX.

## Data & Model Plan

### Datasets (examples to use):
-   FakeNewsNet, LIAR dataset, Kaggle “Fake and real news” dataset — merge and balance if needed.

### Preprocessing:
-   Lowercase, punctuation removal, tokenization, stopword removal, lemmatization.
-   Sentence splitting (for per-sentence scoring / highlighting).

### Vectorization:
-   TF-IDF (unigrams + bi-grams; limit vocab, use min_df/max_df).

### Model(s):
-   **Baseline**: Logistic Regression (fast, interpretable).
-   **Compare**: Linear SVM, Random Forest, Naive Bayes.
-   Pick best performing model (balanced accuracy / F1).

### Explainability Tools:
-   **LIME** to produce per-article local explanations (tokens and weights).
-   **TF-IDF score mapping** for highlight ranking (fast, used in extension).

## High-level Data Flow
1.  User opens article → extension content script extracts text.
2.  Extension sends text to Laravel endpoint (`/api/check`).
3.  Laravel logs request and forwards text to Python ML API `/predict`.
4.  Python API returns `{prediction, confidence, tfidf_tokens, lime_id}`.
5.  Laravel returns response to extension; extension highlights tokens and shows a popup.
6.  User can click “Open full explanation” → Laravel opens an explain page which fetches LIME explanation from Python API and visualizes it.

## UX & Features
-   **Browser extension**: Auto-detect article + popup result; inline highlight (red/amber/green); quick confidence %; link to detailed report.
-   **Web app (public)**: Paste text, check, see results, explanation link.
-   **Admin dashboard**: View all checks, filter by result/confidence, download logs, upload new training data CSV, trigger retrain, view model metrics & confusion matrix, rollback to previous model.
-   **Explainability UI**: LIME token bar chart (positive vs negative influence), sample highlighted sentences, TF-IDF top tokens table.
