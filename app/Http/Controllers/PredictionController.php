<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class PredictionController extends Controller
{
    public function predict(Request $request)
    {
        // 1. Validate Input
        $request->validate([
            'text' => 'required|string|max:1000',
            'model' => 'required|in:rnn,lstm',
        ]);

        $text = $request->input('text');
        $model = $request->input('model');
        try {

            $response = Http::post('http://127.0.0.1:5000/predict', [
                'text' => $text,
                'model_type' => $model,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI Service is unreachable.'
            ], 503);
        }

        // 3. Process Response
        if ($response->successful()) {
            $data = $response->json();

            $formattedResponse = [
                'label' => $data['label'],
                'confidence' => $data['score'],
                'meta' => [
                    'time_ms' => $data['time_ms'],
                    'model' => $data['model_used']
                ]
            ];



            return response()->json([
                'success' => true,
                'data' => $formattedResponse
            ]);
        }

        // 4. Handle AI Errors
        return response()->json([
            'success' => false,
            'message' => 'AI analysis failed.',
            'debug' => $response->body()
        ], 500);
    }
}
