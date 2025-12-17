import requests
import json

API_URL = "http://127.0.0.1:8000"

def test_predict():
    print("Testing /predict...")
    text = "Breaking: Aliens have landed in New York City and are demanding pizza."
    response = requests.post(f"{API_URL}/predict", json={"text": text})
    if response.status_code == 200:
        print("Success:", response.json())
    else:
        print("Failed:", response.text)

def test_explain():
    print("\nTesting /explain...")
    text = "Breaking: Aliens have landed in New York City and are demanding pizza."
    response = requests.post(f"{API_URL}/explain", json={"text": text})
    if response.status_code == 200:
        data = response.json()
        print("Success. Explanation tokens found:", len(data['explanation']))
        print("Top 3 tokens:", data['explanation'][:3])
    else:
        print("Failed:", response.text)

if __name__ == "__main__":
    try:
        test_predict()
        test_explain()
    except Exception as e:
        print(f"Error connecting to API: {e}")
        print("Make sure uvicorn is running!")
