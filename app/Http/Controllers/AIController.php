<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $apiKey = config('services.openrouter.api_key');

        \Log::info('API Key exists: ' . (!empty($apiKey) ? 'yes' : 'no'));
        
        if (!$apiKey) {
            return response()->json([
                'reply' => 'Maaf, API key belum dikonfigurasi.'
            ]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => 'http://localhost',
                'X-Title' => 'SocialChat',
            ])->timeout(60)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'openai/gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Kamu adalah Social Chat AI, asisten virtual yang helpful, friendly dan menggunakan bahasa Indonesia yang natural dan singkat. Jangan terlalu panjang dalam menjawab.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $request->message
                    ]
                ],
                'max_tokens' => 300,
            ]);

            \Log::info('OpenRouter Response: ' . $response->body());

            $data = $response->json();

            if (isset($data['choices'][0]['message']['content'])) {
                return response()->json([
                    'reply' => $data['choices'][0]['message']['content']
                ]);
            }

            return response()->json([
                'reply' => 'Maaf, respons tidak valid.'
            ], 500);

        } catch (\Exception $e) {
            \Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'reply' => 'Maaf, terjadi kesalahan. Silakan coba lagi.'
            ], 500);
        }
    }
}
