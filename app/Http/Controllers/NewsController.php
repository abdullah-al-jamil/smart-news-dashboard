<?php
namespace App\Http\Controllers;

use App\Services\NewsApiService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected $newsService;

    public function __construct(NewsApiService $newsService)
    {
        $this->newsService = $newsService;
    }

    public function index(Request $request)
    {
        $category = $request->get('category', 'general');
        $articles = $this->newsService->fetchTopHeadlines($category);
        return view('news.index', compact('articles', 'category'));
    }

    public function search(Request $request)
    {
        // Optional: integrate News API's /everything endpoint
        // For simplicity, we'll reuse top-headlines with a keyword filter client-side
        return redirect()->route('news.index', ['category' => $request->q]);
    }
}