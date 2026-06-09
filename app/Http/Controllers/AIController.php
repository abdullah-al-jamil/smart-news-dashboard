<?php
namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function summarize(Request $request)
    {
        $request->validate(['content' => 'required|string|min:20']);
        $summary = $this->aiService->summarize($request->content);
        return response()->json(['summary' => $summary]);
    }

    public function chat(Request $request)
    {
        $request->validate([
            'article_content' => 'required|string',
            'question' => 'required|string|max:500'
        ]);
        $answer = $this->aiService->askQuestion($request->article_content, $request->question);
        return response()->json(['answer' => $answer]);
    }
}