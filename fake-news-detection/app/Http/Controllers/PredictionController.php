<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function index()
    {
        $recentPredictions = \App\Models\PredictionHistory::latest()->take(5)->get();
        return view('prediction.index', ['recentPredictions' => $recentPredictions]);
    }

    public function predict(Request $request, \App\Services\PredictionService $predictionService, \App\Services\ScraperService $scraperService)
    {
        $request->validate([
            'news_text' => 'required_without:news_url|nullable|string|min:10|max:10000',
            'news_url' => 'required_without:news_text|nullable|url',
        ]);

        try {
            $textToAnalyze = $request->input('news_text');

            if ($request->filled('news_url')) {
                $textToAnalyze = $scraperService->scrape($request->input('news_url'));
            }

            $result = $predictionService->predict($textToAnalyze);

            \App\Models\PredictionHistory::create([
                'news_text' => $textToAnalyze,
                'prediction' => $result['prediction'],
                'confidence_score' => $result['confidence_score'],
            ]);

            $recentPredictions = \App\Models\PredictionHistory::latest()->take(5)->get();

            return view('prediction.index', [
                'result' => $result,
                'news_text' => $textToAnalyze,
                'recentPredictions' => $recentPredictions,
                'news_url' => $request->input('news_url'), // Pass back URL to keep input filled
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['api_error' => $e->getMessage()])->withInput();
        }
    }
    public function history($id = null)
    {
        $history = \App\Models\PredictionHistory::latest()->get();
        $selectedPrediction = $id ? \App\Models\PredictionHistory::findOrFail($id) : null;

        return view('prediction.history', [
            'history' => $history,
            'selectedPrediction' => $selectedPrediction,
        ]);
    }
}
