<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->apiUrl = env('GEMINI_API_URL');
    }

    /**
     * Summarize article text
     */
    public function summarize($text, $maxLength = 150)
    {
        $prompt = "Summarize the following news article in 4 to 5 sentences (max {$maxLength} characters):\n\n{$text}";
        return $this->callGemini($prompt);
    }

    /**
     * Answer user's question about an article
     */
    public function askQuestion($articleText, $question)
    {
        $prompt = "You are a helpful news assistant. Based on the following article:\n\n{$articleText}\n\nAnswer this question concisely: {$question}";
        return $this->callGemini($prompt);
    }

    /**
     * Generic Gemini API caller
     */
    protected function callGemini($prompt)
    {
        $response = Http::post($this->apiUrl . '?key=' . $this->apiKey, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 700,
            ]
        ]);

        if ($response->failed()) {
            \Log::error('Gemini API error', ['body' => $response->body()]);
            return 'Sorry, the AI service is temporarily unavailable.';
        }

        $data = $response->json();
        // Gemini returns: candidates[0].content.parts[0].text
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response from AI.';
    }
}