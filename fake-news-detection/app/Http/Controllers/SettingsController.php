<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.ml_model.url');
    }

    public function index()
    {
        $status = [];
        try {
            $response = Http::get("{$this->baseUrl}/status");
            if ($response->successful()) {
                $status = $response->json();
            }
        } catch (\Exception $e) {
            $status = ['error' => 'Could not connect to ML Backend'];
        }

        return view('settings.index', ['status' => $status]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'fake_csv' => 'nullable|file|mimes:csv,txt',
            'true_csv' => 'nullable|file|mimes:csv,txt',
        ]);

        if (!$request->hasFile('fake_csv') && !$request->hasFile('true_csv')) {
            return back()->withErrors(['error' => 'Please upload at least one CSV file.']);
        }

        try {
            $response = Http::asMultipart();
            
            if ($request->hasFile('fake_csv')) {
                $file = $request->file('fake_csv');
                $response->attach('fake_csv', fopen($file->path(), 'r'), 'Fake.csv');
            }

            if ($request->hasFile('true_csv')) {
                $file = $request->file('true_csv');
                $response->attach('true_csv', fopen($file->path(), 'r'), 'True.csv');
            }

            $result = $response->post("{$this->baseUrl}/upload-data");

            if ($result->successful()) {
                return back()->with('success', 'Datasets uploaded successfully!');
            } else {
                return back()->withErrors(['api_error' => 'Upload failed: ' . $result->body()]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['api_error' => $e->getMessage()]);
        }
    }

    public function retrain(Request $request)
    {
        $request->validate([
            'max_features' => 'required|integer|min:1000|max:50000',
            'max_iter' => 'required|integer|min:100|max:5000',
        ]);

        try {
            $response = Http::post("{$this->baseUrl}/retrain", [
                'max_features' => $request->input('max_features'),
                'max_iter' => $request->input('max_iter'),
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Training started in background! Check status later.');
            } else {
                return back()->withErrors(['api_error' => 'Retraining failed: ' . $response->body()]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['api_error' => $e->getMessage()]);
        }
    }
}
