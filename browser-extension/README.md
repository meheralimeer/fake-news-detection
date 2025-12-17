# NewsGuard Browser Extension

## Installation Instructions

### Chrome / Edge
1. Open `chrome://extensions/` in your browser
2. Enable "Developer mode" (toggle in top-right)
3. Click "Load unpacked"
4. Select the `browser-extension` folder
5. The extension should now appear in your extensions bar

### Firefox
1. Open `about:debugging#/runtime/this-firefox`
2. Click "Load Temporary Add-on"
3. Navigate to the `browser-extension` folder
4. Select `manifest.json`
5. The extension will be loaded temporarily (until Firefox restarts)

## Usage

1. **Navigate to a news article** on any website
2. **Click the NewsGuard extension icon** in your browser toolbar
3. **Click "Analyze This Article"** to get the fake news detection result
4. **View the prediction** (Real/Fake) with confidence score
5. **Click "Highlight on Page"** to see suspicious words highlighted inline
6. **Click "View Explanation"** to open the full LIME explanation in the web app

## Features

- ✅ Automatic article text extraction
- ✅ Real-time fake news detection
- ✅ Confidence score display
- ✅ Inline word highlighting (green for real, red for fake)
- ✅ Detailed LIME explanations via web app link
- ✅ Works on most news websites

## Configuration

The extension is configured to communicate with:
- **Laravel API**: `http://localhost:8000`

To change the API URL, edit `background.js` and update the `API_BASE_URL` constant.

## Notes

- The extension requires the Laravel backend to be running
- Article text must be at least 100 characters
- Results are cached per page for quick access
