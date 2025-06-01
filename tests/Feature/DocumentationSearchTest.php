<?php

use App\Console\Commands\BuildDocsSearchIndex;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

test('search index command creates json file', function () {
    // Delete existing index if it exists
    $indexPath = public_path('docs-search-index.json');
    if (File::exists($indexPath)) {
        File::delete($indexPath);
    }
    
    // Run the command
    Artisan::call('command:build-index');
    
    // Assert the file was created
    expect(File::exists($indexPath))->toBeTrue();
    
    // Assert the file contains valid JSON
    $content = File::get($indexPath);
    $json = json_decode($content, true);
    expect($json)->toBeArray();
    expect($json)->not->toBeEmpty();
});

test('search index contains expected document structure', function () {
    // Run the command to ensure fresh index
    Artisan::call('command:build-index');
    
    $indexPath = public_path('docs-search-index.json');
    $content = File::get($indexPath);
    $documents = json_decode($content, true);
    
    // Check that we have documents
    expect($documents)->toBeArray();
    expect(count($documents))->toBeGreaterThan(0);
    
    // Check document structure
    $firstDoc = $documents[0];
    expect($firstDoc)->toHaveKeys(['id', 'title', 'content', 'headings', 'url', 'section', 'breadcrumb']);
    
    // Check specific documents exist
    $ids = array_column($documents, 'id');
    expect($ids)->toContain('security');
    expect($ids)->toContain('quick-start');
    expect($ids)->toContain('forms/overview');
    expect($ids)->toContain('forms/field-types');
});

test('search index properly extracts content', function () {
    Artisan::call('command:build-index');
    
    $indexPath = public_path('docs-search-index.json');
    $content = File::get($indexPath);
    $documents = json_decode($content, true);
    
    // Find the security document
    $securityDoc = collect($documents)->firstWhere('id', 'security');
    
    expect($securityDoc)->not->toBeNull();
    expect($securityDoc['title'])->toBe('Security');
    expect($securityDoc['url'])->toBe('/security');
    expect($securityDoc['section'])->toBe('Documentation');
    expect($securityDoc['content'])->toContain('Security at Flow Forms');
    expect($securityDoc['headings'])->toContain('Security at Flow Forms');
});

test('search index handles nested folders correctly', function () {
    Artisan::call('command:build-index');
    
    $indexPath = public_path('docs-search-index.json');
    $content = File::get($indexPath);
    $documents = json_decode($content, true);
    
    // Find a forms document
    $formsDoc = collect($documents)->firstWhere('id', 'forms/overview');
    
    expect($formsDoc)->not->toBeNull();
    expect($formsDoc['title'])->toBe('Overview');
    expect($formsDoc['url'])->toBe('/forms/overview');
    expect($formsDoc['section'])->toBe('Forms');
    expect($formsDoc['breadcrumb'])->toBe('Forms › Overview');
});

test('search component is rendered in docs layout', function () {
    $response = $this->get('/security');
    
    $response->assertOk();
    $response->assertSee('x-data="docsSearch()"', false);
    $response->assertSee('Search documentation... (⌘K)', false);
});

