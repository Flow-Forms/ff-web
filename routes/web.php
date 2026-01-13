<?php

use App\Helpers\MarkdownHelper;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.index');
});

// Raw markdown routes for LLMs
Route::get('/index.md', function () {
    $indexPath = MarkdownHelper::getIndexDocumentPath();
    if (! $indexPath) {
        abort(404, 'Documentation page not found');
    }

    return response(MarkdownHelper::getRawContentFromPath($indexPath), 200, [
        'Content-Type' => 'text/markdown; charset=UTF-8',
    ]);
});

Route::get('/{slug}.md', function (string $slug) {
    if (! MarkdownHelper::markdownExists($slug)) {
        abort(404, 'Documentation page not found');
    }

    return response(MarkdownHelper::getRawContent($slug), 200, [
        'Content-Type' => 'text/markdown; charset=UTF-8',
    ]);
})->where('slug', '[a-z0-9-]+');

Route::get('/{folder}/{slug}.md', function (string $folder, string $slug) {
    if (! MarkdownHelper::markdownExists($slug, $folder)) {
        abort(404, 'Documentation page not found');
    }

    return response(MarkdownHelper::getRawContent($slug, $folder), 200, [
        'Content-Type' => 'text/markdown; charset=UTF-8',
    ]);
})->where(['folder' => '[a-z0-9-]+', 'slug' => '[a-z0-9-]+']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
