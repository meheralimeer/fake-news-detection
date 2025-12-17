# GEMINI Project Analysis: Fake News Detection

This document provides a comprehensive analysis of the "fake-news-detection" project, intended to be used as a context for future development and interaction.

## Project Overview

This project is a "fake news detection" application composed of two main parts:

1.  **A web application backend** built with the [Laravel](https://laravel.com/) framework (version 12). This is likely responsible for the user interface, user management, and interacting with the machine learning model.
2.  **A machine learning backend** built in Python. This component serves a pre-trained Convolutional Neural Network (CNN) model for classifying news articles as "real" or "fake".

The project is structured into three main directories:

*   `fake-news-detection/`: The Laravel application.
*   `ml_model/`: Contains the Jupyter Notebook (`model_training.ipynb`) used to train the fake news detection model.
*   `ml_backend/`: Contains the trained Keras model (`fake_news_cnn.keras`), the tokenizer (`tokenizer.pkl`), and likely the Python API (using FastAPI, as suggested by `requirments.txt`) to serve the model.

## Laravel Application (fake-news-detection/)

This is a standard Laravel 12 application.

### Dependencies

*   **PHP**: `^8.2`
*   **Laravel**: `^12.0`
*   **Testing**: Pest (`^4.2`)
*   **Code Style**: Laravel Pint (`^1.24`)
*   **Frontend**: HTML, CSS, Javascript (vanilla), Bootstrap, TailwindCSS

### Running the Application

*   **Setup**:
    ```bash
    composer run setup
    ```
    This will install composer and npm dependencies, create a `.env` file, generate an application key, run database migrations and build the frontend assets.

*   **Development**:
    ```bash
    composer run dev
    ```
    This command will concurrently start the PHP development server, the queue listener, the log watcher, and the Vite server for frontend development.

*   **Testing**:
    ```bash
    composer run test
    ```
    This will run the Pest test suite.

## Machine Learning Backend (ml_backend/ & ml_model/)

The machine learning component is a Python application that uses a trained TensorFlow/Keras model.

### Dependencies

The Python dependencies are listed in `requirments.txt`:

*   `tensorflow`
*   `scikit-learn`
*   `numpy`
*   `pandas`
*   `fastapi`
*   `pickle`
*   `uvicorn`
*   `tqdm`
*   `seaborn`

### Model

The model is a Convolutional Neural Network (CNN) for text classification, trained on a dataset of real and fake news articles. The training process is documented in `ml_model/model_training.ipynb`. The trained model is saved as `ml_backend/fake_news_cnn.keras`.

### Running the ML Backend

To run the ML backend, you would typically start a `uvicorn` server for the FastAPI application. A `main.py` or `app.py` file is expected to be in the `ml_backend` directory (though not currently present).

Assuming a file named `main.py` with a FastAPI app instance named `app`:

```bash
# First install dependencies
pip install -r requirments.txt

# Run the server from the ml_backend directory
cd ml_backend
uvicorn main:app --reload
```
**TODO**: Create the `main.py` file for the FastAPI server in `ml_backend/`.

## Development Conventions

*   **Code Style**: The PHP code should adhere to the Laravel Pint code style. Run `vendor/bin/pint` to format the code.
*   **Testing**: All new PHP features should be accompanied by Pest tests.
*   **Database**: Laravel's Eloquent ORM and migrations should be used for all database interactions.
