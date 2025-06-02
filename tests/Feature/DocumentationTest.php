<?php

use function Pest\Laravel\get;

it('displays the security documentation page', function () {
    $response = get('/security');
    
    $response->assertOk();
    $response->assertSee('Security at Flow Forms');
    $response->assertSee('Security Framework');
    $response->assertSee('Infrastructure Security');
    $response->assertSee('Application Security');
    $response->assertSee('Data Privacy & Compliance');
});

it('renders markdown content from security.md file', function () {
    $response = get('/security');
    
    $response->assertOk();
    // Check for specific markdown-rendered content
    $response->assertSee('CIS (Center for Internet Security) Controls');
    $response->assertSee('Amazon Web Services (AWS)');
    $response->assertSee('Neon Postgres');
    $response->assertSee('security@flowforms.com');
});

it('displays documentation with proper layout', function () {
    $response = get('/security');
    
    $response->assertOk();
    $response->assertSee('Security at Flow Forms');
    $response->assertSee('Flow Forms', false); // Check for layout header
    $response->assertSee('Security'); // Check for dynamic nav item
});

it('displays the documentation homepage', function () {
    $response = get('/');
    
    $response->assertOk();
    $response->assertSee('Flow Forms Documentation');
});

it('dynamically creates routes for new markdown files', function () {
    // Create a test markdown file
    $testContent = "# Test Documentation\n\nThis is a test page.";
    file_put_contents(resource_path('markdown/test-page.md'), $testContent);
    
    $response = get('/test-page');
    
    $response->assertOk();
    $response->assertSee('Test Documentation');
    $response->assertSee('This is a test page');
    
    // Clean up
    unlink(resource_path('markdown/test-page.md'));
});

it('returns 404 for non-existent markdown files', function () {
    $response = get('/non-existent-page');
    
    $response->assertNotFound();
});

it('generates navigation items from markdown files', function () {
    $navigationItems = \App\Helpers\MarkdownHelper::getNavigationItems();
    
    expect($navigationItems)->toBeArray();
    expect($navigationItems)->not->toBeEmpty();
    
    // Check that security.md appears in root navigation
    expect($navigationItems['_root'])->toBeArray();
    $securityItem = collect($navigationItems['_root'])->firstWhere('filename', 'security');
    expect($securityItem)->not->toBeNull();
    expect($securityItem['title'])->toBe('Security');
    expect($securityItem['url'])->toBe('/security');
});

it('handles nested folder documentation', function () {
    $response = get('/forms/overview');
    
    $response->assertOk();
    $response->assertSee('Forms Overview');
    $response->assertSee('What are Flow Forms?');
});

it('displays nested pages with proper navigation', function () {
    $response = get('/forms/field-types');
    
    $response->assertOk();
    $response->assertSee('Field Types');
    $response->assertSee('Text Fields');
    $response->assertSee('Selection Fields');
});

it('generates grouped navigation for folders', function () {
    $navigationItems = \App\Helpers\MarkdownHelper::getNavigationItems();
    
    // Check that forms folder exists in navigation
    expect($navigationItems['forms'])->toBeArray();
    expect($navigationItems['forms']['type'])->toBe('folder');
    expect($navigationItems['forms']['title'])->toBe('Forms');
    expect($navigationItems['forms']['items'])->toBeArray();
    
    // Check that folder contains expected items
    $folderItems = $navigationItems['forms']['items'];
    $overviewItem = collect($folderItems)->firstWhere('filename', 'overview');
    expect($overviewItem['title'])->toBe('Overview');
    expect($overviewItem['url'])->toBe('/forms/overview');
    expect($overviewItem['folder'])->toBe('forms');
});

it('returns 404 for non-existent nested pages', function () {
    $response = get('/forms/non-existent');
    
    $response->assertNotFound();
});

it('creates dynamic routes for new nested files', function () {
    // Create test nested file
    $testContent = "# Test Nested Page\n\nThis is a test nested page.";
    $folderPath = resource_path('markdown/api');
    
    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }
    
    file_put_contents($folderPath . '/authentication.md', $testContent);
    
    $response = get('/api/authentication');
    
    $response->assertOk();
    $response->assertSee('Test Nested Page');
    $response->assertSee('This is a test nested page');
    
    // Clean up
    unlink($folderPath . '/authentication.md');
    if (is_dir($folderPath) && count(scandir($folderPath)) === 2) { // only . and ..
        rmdir($folderPath);
    }
});

// Comprehensive accessibility tests for all existing markdown files
describe('All markdown files are accessible', function () {
    it('can access all root level markdown files', function () {
        $rootFiles = [
            'security' => 'Security at Flow Forms',
            'getting-started' => 'Getting Started Guide'
        ];
        
        foreach ($rootFiles as $slug => $expectedContent) {
            $response = get("/{$slug}");
            $response->assertOk();
            
            if ($expectedContent) {
                $response->assertSee($expectedContent);
            }
        }
    });
    
    it('can access all forms folder markdown files', function () {
        $formsFiles = [
            'overview' => 'Forms Overview',
            'field-types' => 'Field Types',
        ];
        
        foreach ($formsFiles as $slug => $expectedTitle) {
            $response = get("/forms/{$slug}");
            $response->assertOk();
            $response->assertSee($expectedTitle);
        }
    });
});

describe('Navigation links work correctly', function () {
    it('has working navigation links for all pages', function () {
        $navigationItems = \App\Helpers\MarkdownHelper::getNavigationItems();
        
        // Test root level navigation links
        if (isset($navigationItems['_root'])) {
            foreach ($navigationItems['_root'] as $item) {
                $response = get($item['url']);
                $response->assertOk();
            }
        }
        
        // Test folder navigation links
        foreach ($navigationItems as $key => $section) {
            if ($key !== '_root' && isset($section['items'])) {
                foreach ($section['items'] as $item) {
                    $response = get($item['url']);
                    $response->assertOk();
                }
            }
        }
    });
    
    it('navigation contains all expected files', function () {
        $navigationItems = \App\Helpers\MarkdownHelper::getNavigationItems();
        
        // Check root files are in navigation
        $rootFilenames = collect($navigationItems['_root'] ?? [])->pluck('filename')->toArray();
        expect($rootFilenames)->toContain('security');
        expect($rootFilenames)->toContain('getting-started');
        
        // Check forms folder exists and contains expected files
        expect($navigationItems)->toHaveKey('forms');
        $formsFilenames = collect($navigationItems['forms']['items'])->pluck('filename')->toArray();
        expect($formsFilenames)->toContain('overview');
        expect($formsFilenames)->toContain('field-types');
    });
});

describe('Markdown parsing works correctly', function () {
    it('properly renders markdown content for all files', function () {
        // Test that markdown headers are converted to HTML
        $response = get('/security');
        $response->assertOk();
        $response->assertSee('<h1>Security at Flow Forms</h1>', false);
        $response->assertSee('<h2>Security Framework</h2>', false);
        
        $response = get('/forms/overview');
        $response->assertOk();
        $response->assertSee('<h1>Forms Overview</h1>', false);
        $response->assertSee('<h2>What are Flow Forms?</h2>', false);
        
        $response = get('/forms/field-types');
        $response->assertOk();
        $response->assertSee('<h1>Field Types</h1>', false);
        $response->assertSee('<h2>Text Fields</h2>', false);
    });
    
    it('renders markdown links correctly', function () {
        $response = get('/forms/overview');
        $response->assertOk();
        // Check that markdown links are converted to HTML links
        $response->assertSee('<a href="/forms/field-types">Field Types</a>', false);
        
        $response = get('/forms/field-types');
        $response->assertOk();
        $response->assertSee('<a href="/forms/overview">Forms Overview</a>', false);
    });
    
    it('renders markdown lists correctly', function () {
        $response = get('/security');
        $response->assertOk();
        // Check that markdown lists are converted to HTML
        $response->assertSee('<ul>', false);
        $response->assertSee('<li>', false);
        
        $response = get('/forms/field-types');
        $response->assertOk();
        $response->assertSee('<ul>', false);
        $response->assertSee('<li>', false);
    });
});

describe('Dynamic file discovery and accessibility', function () {
    it('can access every markdown file discovered in the filesystem', function () {
        $markdownPath = resource_path('markdown');
        $allFiles = [];
        
        // Recursively find all .md files
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($markdownPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'md' && $file->getFilename() !== 'README.md') {
                $relativePath = str_replace($markdownPath . '/', '', $file->getPathname());
                // Remove .md extension
                $url = '/' . str_replace('.md', '', $relativePath);
                // Remove numeric prefix from URLs
                $url = preg_replace('/\/\d{2}-/', '/', $url);
                $url = preg_replace('/^\/\d{2}-/', '/', $url);
                $allFiles[] = $url;
            }
        }
        
        // Test that every discovered file is accessible
        foreach ($allFiles as $url) {
            $response = get($url);
            $response->assertOk("Failed to access: {$url}");
        }
        
        // Ensure we found at least the files we know should exist
        expect($allFiles)->toContain('/security');
        expect($allFiles)->toContain('/getting-started');
        expect($allFiles)->toContain('/forms/overview');
        expect($allFiles)->toContain('/forms/field-types');
    });
});