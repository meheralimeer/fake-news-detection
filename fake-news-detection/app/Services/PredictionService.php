<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class PredictionService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.ml_model.url');
    }

    /**
     * Send text to the ML model for prediction.
     *
     * @param string $text
     * @return array
     * @throws \Exception
     */
    public function predict(string $text): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/predict", [
                'text' => $text,
            ]);

            if ($response->failed()) {
                throw new \Exception('Failed to get prediction from ML model: ' . $response->body());
            }

            return $response->json();
        } catch (ConnectionException $e) {
            throw new \Exception('Could not connect to the ML backend. Is it running?');
        }
    }
}
