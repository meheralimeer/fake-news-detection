<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PredictionService;
use App\Models\PredictionHistory;

class ExtensionController extends Controller
{
    public function check(Request $request, PredictionService $predictionService)
    {
        $request->validate([
            'text' => 'required|string|min:100',
            'url' => 'nullable|url',
            'title' => 'nullable|string'
        ]);

        try {
            $text = $request->input('text');
            
            // Get prediction and explanation from ML API
            $result = $predictionService->predict($text);
            $explanation = $predictionService->explain($text);

            // Save to history
            $history = PredictionHistory::create([
                'news_text' => $text,
                'prediction' => $result['prediction'],
                'confidence_score' => $result['confidence_score'],
                'explanation' => $explanation,
            ]);

            return response()->json([
                'success' => true,
                'prediction' => $result['prediction'],
                'confidence_score' => $result['confidence_score'],
                'explanation' => $explanation,
                'history_id' => $history->id,
                'url' => $request->input('url'),
                'title' => $request->input('title')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
