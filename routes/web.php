<?php

use App\Http\Controllers\NewsController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\BookmarkController;

Route::get('/', [NewsController::class, 'index'])->name('news.index');
Route::get('/category/{category}', [NewsController::class, 'index'])->name('news.category');

Route::post('/ai/summarize', [AIController::class, 'summarize'])->name('ai.summarize');
Route::post('/ai/chat', [AIController::class, 'chat'])->name('ai.chat');

Route::middleware('auth')->group(function () {
    Route::post('/bookmark/{article}', [BookmarkController::class, 'toggle'])->name('bookmark.toggle');
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
});

// require __DIR__.'/auth.php'; // if using Laravel Breeze/Jetstream for authentication