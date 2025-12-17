// Background service worker - handles API communication

const API_BASE_URL = 'http://localhost:8000';

// Listen for messages from popup or content script
chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
    if (request.action === 'analyze') {
        analyzeArticle(request.data)
            .then(result => sendResponse({ success: true, result }))
            .catch(error => sendResponse({ success: false, error: error.message }));
        return true; // Keep channel open for async response
    }
});

// Analyze article by calling Laravel API
async function analyzeArticle(articleData) {
    try {
        const response = await fetch(`${API_BASE_URL}/api/extension/check`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                text: articleData.text,
                url: articleData.url,
                title: articleData.title
            })
        });

        if (!response.ok) {
            throw new Error(`API error: ${response.status}`);
        }

        const data = await response.json();

        // Store result in chrome.storage for popup
        await chrome.storage.local.set({
            lastResult: {
                ...data,
                timestamp: Date.now(),
                url: articleData.url
            }
        });

        return data;
    } catch (error) {
        console.error('Analysis failed:', error);
        throw error;
    }
}
