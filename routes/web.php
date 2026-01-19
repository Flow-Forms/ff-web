<?php

use App\Helpers\MarkdownHelper;
use App\Http\Controllers\BunnyWebhookController;
use App\Http\Middleware\VerifyBunnyWebhookSignature;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.index');
});

// Bunny Stream webhook (no CSRF, verified by signature)
Route::post('/webhooks/bunny', BunnyWebhookController::class)
    ->name('webhooks.bunny')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
    ->middleware(VerifyBunnyWebhookSignature::class);

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
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/admin/video', function () {
        return view('admin.video');
    })->name('admin.video');
});
