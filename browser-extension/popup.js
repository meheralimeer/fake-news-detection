// Popup script - handles UI logic

document.addEventListener('DOMContentLoaded', async () => {
    const elements = {
        loading: document.getElementById('loading'),
        error: document.getElementById('error'),
        noArticle: document.getElementById('no-article'),
        result: document.getElementById('result'),
        analyzeSection: document.getElementById('analyze-section'),
        analyzeBtn: document.getElementById('analyze-btn'),
        retryBtn: document.getElementById('retry-btn'),
        highlightBtn: document.getElementById('highlight-btn'),
        detailsBtn: document.getElementById('details-btn'),
        predictionBadge: document.getElementById('prediction-badge'),
        confidenceFill: document.getElementById('confidence-fill'),
        confidenceText: document.getElementById('confidence-text'),
        articleTitle: document.getElementById('article-title'),
        errorMessage: document.getElementById('error-message')
    };

    // Hide all states
    function hideAll() {
        Object.values(elements).forEach(el => {
            if (el) el.classList.add('hidden');
        });
    }

    // Show specific state
    function showState(stateName) {
        hideAll();
        if (elements[stateName]) {
            elements[stateName].classList.remove('hidden');
        }
    }

    // Display result
    function displayResult(result) {
        showState('result');

        // Set prediction badge
        const isFake = result.prediction === 'Fake';
        elements.predictionBadge.textContent = isFake ? '⚠️ FAKE NEWS' : '✅ REAL NEWS';
        elements.predictionBadge.className = `badge ${isFake ? 'badge-fake' : 'badge-real'}`;

        // Set confidence
        const confidence = Math.round(result.confidence_score * 100);
        elements.confidenceFill.style.width = `${confidence}%`;
        elements.confidenceFill.className = `progress-fill ${isFake ? 'fake' : 'real'}`;
        elements.confidenceText.textContent = `${confidence}%`;

        // Set article title
        if (result.title) {
            elements.articleTitle.textContent = result.title;
        }
    }

    // Analyze current page
    async function analyzePage() {
        try {
            showState('loading');

            // Get current tab
            const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });

            // Extract article from page
            const articleData = await chrome.tabs.sendMessage(tab.id, { action: 'extract' });

            if (!articleData || !articleData.text) {
                showState('noArticle');
                return;
            }

            // Send to background for analysis
            const response = await chrome.runtime.sendMessage({
                action: 'analyze',
                data: articleData
            });

            if (response.success) {
                displayResult(response.result);

                // Send highlight message to content script
                await chrome.tabs.sendMessage(tab.id, {
                    action: 'highlight',
                    result: response.result
                });
            } else {
                throw new Error(response.error || 'Analysis failed');
            }
        } catch (error) {
            console.error('Analysis error:', error);
            showState('error');
            elements.errorMessage.textContent = error.message || 'Failed to analyze article. Please try again.';
        }
    }

    // Check for cached result
    const storage = await chrome.storage.local.get(['lastResult']);
    const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });

    if (storage.lastResult && storage.lastResult.url === tab.url) {
        // Show cached result
        displayResult(storage.lastResult);
    } else {
        // Show analyze button
        showState('analyzeSection');
    }

    // Event listeners
    elements.analyzeBtn?.addEventListener('click', analyzePage);
    elements.retryBtn?.addEventListener('click', analyzePage);

    elements.highlightBtn?.addEventListener('click', async () => {
        const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });
        const result = await chrome.storage.local.get(['lastResult']);
        if (result.lastResult) {
            await chrome.tabs.sendMessage(tab.id, {
                action: 'highlight',
                result: result.lastResult
            });
        }
    });

    elements.detailsBtn?.addEventListener('click', async () => {
        const result = await chrome.storage.local.get(['lastResult']);
        if (result.lastResult && result.lastResult.history_id) {
            chrome.tabs.create({
                url: `http://localhost:8000/history/${result.lastResult.history_id}`
            });
        }
    });
});
