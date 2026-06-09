<?php
namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function toggle(Article $article)
    {
        $user = Auth::user();
        if ($user->bookmarkedArticles()->where('article_id', $article->id)->exists()) {
            $user->bookmarkedArticles()->detach($article->id);
            $bookmarked = false;
        } else {
            $user->bookmarkedArticles()->attach($article->id);
            $bookmarked = true;
        }
        return response()->json(['bookmarked' => $bookmarked]);
    }

    public function index()
    {
        $articles = Auth::user()->bookmarkedArticles()->paginate(10);
        return view('news.bookmarks', compact('articles'));
    }
}