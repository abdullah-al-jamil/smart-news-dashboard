<?php

use App\Http\Controllers\NewsController;
use App\Http\Controllers\AIController;
Route::get('/', [NewsController::class, 'index'])->name('news.index');
Route::get('/category/{category}', [NewsController::class, 'index'])->name('news.category');

Route::post('/ai/summarize', [AIController::class, 'summarize'])->name('ai.summarize');
Route::post('/ai/chat', [AIController::class, 'chat'])->name('ai.chat');