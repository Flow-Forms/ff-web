<?php

use function Pest\Laravel\get;

it('displays markdown documentation pages', function () {
    $response = get('/security');

    $response->assertOk();
    // Check that markdown H1 is rendered as HTML
    $response->assertSee('<h1 ', false);
    // Check that markdown content exists (not empty)
    $content = $response->getContent();
    expect(strlen(strip_tags($content)))->toBeGreaterThan(100);
});

it('displays documentation with proper layout', function () {
    $response = get('/security');

    $response->assertOk();
    // Check that layout is present
    $response->assertSee('<!DOCTYPE html>', false);
    $response->assertSee('<html', false);
    // Check that page has title
    expect($response->getContent())->toContain('<title>');
});

it('displays the documentation homepage', function () {
    $response = get('/');

    $response->assertOk();
    // Check that homepage has content
    $content = $response->getContent();
    expect(strlen(strip_tags($content)))->toBeGreaterThan(100);
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

    // Check that security.md appears in navigation
    expect($navigationItems)->toHaveKey('security');
    expect($navigationItems['security']['type'])->toBe('file');
    expect($navigationItems['security']['title'])->toBe('Security');
    expect($navigationItems['security']['url'])->toBe('/security');
});

it('handles nested folder documentation', function () {
    $response = get('/forms/overview');

    $response->assertOk();
    // Check that markdown is rendered
    $response->assertSee('<h1 ', false);
    $content = $response->getContent();
    expect(strlen(strip_tags($content)))->toBeGreaterThan(100);
});

it('displays nested pages with proper navigation', function () {
    $response = get('/forms/field-types');

    $response->assertOk();
    // Check that markdown is rendered
    $response->assertSee('<h1 ', false);
    $response->assertSee('<h2 ', false);
    $content = $response->getContent();
    expect(strlen(strip_tags($content)))->toBeGreaterThan(100);
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
    expect($overviewItem)->not->toBeNull();
    expect($overviewItem['url'])->toBe('/forms/overview');
    expect($overviewItem['folder'])->toBe('forms');
});

it('generates navigation with subfolder items', function () {
    $navigationItems = \App\Helpers\MarkdownHelper::getNavigationItems();

    // The submissions folder should exist and contain sub-folders
    expect($navigationItems)->toHaveKey('submissions');
    expect($navigationItems['submissions']['type'])->toBe('folder');

    $items = $navigationItems['submissions']['items'];

    // Should contain direct files (like notifications.md)
    $directFile = collect($items)->firstWhere('filename', 'notifications');
    expect($directFile)->not->toBeNull();
    expect($directFile['type'])->toBe('file');

    // Should contain a subfolder (managing_submissions)
    $subfolder = collect($items)->firstWhere('type', 'subfolder');
    expect($subfolder)->not->toBeNull();
    expect($subfolder['title'])->toBe('Managing Submissions');
    expect($subfolder['items'])->toBeArray();
    expect($subfolder['items'])->not->toBeEmpty();

    // Subfolder items should include both direct files and leaf-folder files
    $subfolderTitles = collect($subfolder['items'])->pluck('title')->toArray();
    expect($subfolderTitles)->toContain('Filters');
    expect($subfolderTitles)->toContain('Display Options');
});

it('returns 404 for non-existent nested pages', function () {
    $response = get('/forms/non-existent');

    $response->assertNotFound();
});

it('returns 404 instead of 500 when URL contains literal bracket characters', function () {
    // Bot scanners may request URLs with literal [slug] in the path, which causes
    // Folio's MatchLiteralViews to match the filename before MatchWildcardViews
    // can extract the parameter, resulting in a BindingResolutionException.
    get('/*/[slug]')->assertNotFound();
    get('/[slug]')->assertNotFound();
    get('/forms/[slug]')->assertNotFound();
});

it('creates dynamic routes for new nested files', function () {
    // Create test nested file
    $testContent = "# Test Nested Page\n\nThis is a test nested page.";
    $folderPath = resource_path('markdown/api');

    if (! is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }

    file_put_contents($folderPath.'/authentication.md', $testContent);

    $response = get('/api/authentication');

    $response->assertOk();
    $response->assertSee('Test Nested Page');
    $response->assertSee('This is a test nested page');

    // Clean up
    unlink($folderPath.'/authentication.md');
    if (is_dir($folderPath) && count(scandir($folderPath)) === 2) { // only . and ..
        rmdir($folderPath);
    }
});

// Comprehensive accessibility tests for all existing markdown files
describe('All markdown files are accessible', function () {
    it('can access all root level markdown files', function () {
        $rootFiles = [
            'security' => 'Security at Flow Forms',
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

        foreach ($navigationItems as $key => $item) {
            if ($item['type'] === 'file') {
                $response = get($item['url']);
                $response->assertOk();
            } elseif ($item['type'] === 'folder' && isset($item['items'])) {
                foreach ($item['items'] as $subItem) {
                    if ($subItem['type'] === 'file') {
                        $response = get($subItem['url']);
                        $response->assertOk();
                    } elseif ($subItem['type'] === 'subfolder' && isset($subItem['items'])) {
                        foreach ($subItem['items'] as $leafItem) {
                            $response = get($leafItem['url']);
                            $response->assertOk();
                        }
                    }
                }
            }
        }
    });

    it('navigation contains all expected files', function () {
        $navigationItems = \App\Helpers\MarkdownHelper::getNavigationItems();

        // Check root files are in navigation
        expect($navigationItems)->toHaveKey('security');
        expect($navigationItems['security']['type'])->toBe('file');

        // Check forms folder exists and contains expected files
        expect($navigationItems)->toHaveKey('forms');
        $formsFilenames = collect($navigationItems['forms']['items'])->pluck('filename')->toArray();
        expect($formsFilenames)->toContain('overview');
        expect($formsFilenames)->toContain('field-types');
    });
});

describe('Markdown parsing works correctly', function () {
    it('properly renders markdown headings as HTML', function () {
        // Test that markdown headers are converted to HTML (check structure, not content)
        $response = get('/security');
        $response->assertOk();
        $response->assertSee('<h1 ', false);
        $response->assertSee('<h2 ', false);

        $response = get('/forms/overview');
        $response->assertOk();
        $response->assertSee('<h1 ', false);
        $response->assertSee('<h2 ', false);

        $response = get('/forms/field-types');
        $response->assertOk();
        $response->assertSee('<h1 ', false);
        $response->assertSee('<h2 ', false);
    });

    it('renders markdown links as HTML anchors', function () {
        // Check that markdown links are converted to HTML links (structure, not exact content)
        $response = get('/forms/overview');
        $response->assertOk();
        $response->assertSee('<a href=', false);

        $response = get('/forms/field-types');
        $response->assertOk();
        $response->assertSee('<a href=', false);
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

describe('Markdown link normalization', function () {
    it('normalizes same-page anchor links', function () {
        $markdown = '[Link to section](#Filters)';
        $html = \App\Helpers\MarkdownHelper::parse($markdown);

        expect($html)->toContain('href="#filters"');
    });

    it('normalizes internal page links with leading slash', function () {
        $markdown = '[Getting Started](api/getting-started)';
        $html = \App\Helpers\MarkdownHelper::parse($markdown);

        expect($html)->toContain('href="/api/getting-started"');
    });

    it('normalizes internal links with anchors', function () {
        $markdown = '[Token Security](api/getting-started#Token%20Security)';
        $html = \App\Helpers\MarkdownHelper::parse($markdown);

        expect($html)->toContain('href="/api/getting-started#token-security"');
    });

    it('preserves external http links unchanged', function () {
        $markdown = '[Google](https://google.com/)';
        $html = \App\Helpers\MarkdownHelper::parse($markdown);

        expect($html)->toContain('href="https://google.com/"');
    });

    it('preserves external https links unchanged', function () {
        $markdown = '[Docs](https://docs.example.com/path#anchor)';
        $html = \App\Helpers\MarkdownHelper::parse($markdown);

        expect($html)->toContain('href="https://docs.example.com/path#anchor"');
    });

    it('preserves mailto links unchanged', function () {
        $markdown = '[Email Us](mailto:support@example.com)';
        $html = \App\Helpers\MarkdownHelper::parse($markdown);

        expect($html)->toContain('href="mailto:support@example.com"');
    });

    it('handles links that already have leading slashes', function () {
        $markdown = '[Overview](/forms/overview)';
        $html = \App\Helpers\MarkdownHelper::parse($markdown);

        expect($html)->toContain('href="/forms/overview"');
    });

    it('handles anchor-only links with spaces', function () {
        $markdown = '[Quick Filters](#Quick%20Filters)';
        $html = \App\Helpers\MarkdownHelper::parse($markdown);

        expect($html)->toContain('href="#quick-filters"');
    });

    it('generates heading IDs that match anchor link normalization', function () {
        $markdown = <<<'MD'
## My Complex Heading Name

Some content here.

[Jump to heading](#My Complex Heading Name)
MD;

        $html = \App\Helpers\MarkdownHelper::parse($markdown);

        // Extract the heading ID
        preg_match('/id="([^"]+)"/', $html, $idMatch);
        // Extract the anchor href
        preg_match('/href="#([^"]+)"/', $html, $hrefMatch);

        expect($idMatch[1])->toBe($hrefMatch[1])
            ->and($idMatch[1])->toBe('my-complex-heading-name');
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
            if ($file->getExtension() === 'md' && $file->getFilename() !== 'README.md' && $file->getFilename() !== '_meta.md') {
                $relativePath = str_replace($markdownPath.'/', '', $file->getPathname());
                // Remove .md extension
                $url = '/'.str_replace('.md', '', $relativePath);
                // Remove numeric prefix from URLs
                $url = preg_replace('/\/\d{2}-/', '/', $url);
                $url = preg_replace('/^\/\d{2}-/', '/', $url);

                // Detect leaf-folder files: if this is the only content .md in its directory
                // at depth 3+, use the folder name as slug instead of the filename
                $fileDir = dirname($file->getPathname());
                $depth = substr_count(trim(str_replace($markdownPath, '', $fileDir), '/'), '/');

                if ($depth >= 2 && \App\Helpers\MarkdownHelper::isLeafFolder($fileDir)) {
                    // Leaf folder — use folder path as URL, drop the filename
                    $folderRelative = str_replace($markdownPath.'/', '', $fileDir);
                    $url = '/'.$folderRelative;
                }

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
        expect($allFiles)->toContain('/forms/overview');
        expect($allFiles)->toContain('/forms/field-types');
    });
});

it('can access direct files in subfolders via 3-segment URL', function () {
    $response = get('/submissions/managing_submissions/managing_submissions');

    $response->assertOk();
    $response->assertSee('Managing Submissions');
});

it('can access leaf-folder files via clean 3-segment URL', function () {
    $response = get('/submissions/managing_submissions/filters');

    $response->assertOk();
    $response->assertSee('Filters');
});

it('returns 404 for non-existent 3-segment paths', function () {
    $response = get('/submissions/managing_submissions/nonexistent');

    $response->assertNotFound();
});

describe('resolveMarkdownPath', function () {
    it('resolves markdown path for direct subfolder files', function () {
        // URL is lowercase but file is Managing_Submissions.md
        $path = \App\Helpers\MarkdownHelper::resolveMarkdownPath('submissions', 'managing_submissions', 'managing_submissions');

        expect($path)->not->toBeNull();
        expect($path)->toEndWith('Managing_Submissions.md');
        expect(file_exists($path))->toBeTrue();
    });

    it('resolves markdown path for leaf-folder files with clean slug', function () {
        // URL is lowercase but file is Filters.md
        $path = \App\Helpers\MarkdownHelper::resolveMarkdownPath('submissions', 'managing_submissions', 'filters');

        expect($path)->not->toBeNull();
        expect($path)->toEndWith('Filters.md');
        expect(file_exists($path))->toBeTrue();
    });

    it('returns null for non-existent subfolder paths', function () {
        $path = \App\Helpers\MarkdownHelper::resolveMarkdownPath('submissions', 'managing_submissions', 'nonexistent');

        expect($path)->toBeNull();
    });
});

it('renders navigation with subfolder links on homepage', function () {
    $response = get('/');

    $response->assertOk();
    $content = $response->getContent();
    // The navigation should contain links to subfolder items
    expect($content)->toContain('/submissions/managing_submissions/filters');
});

it('renders collapsible subfolder groups in navigation', function () {
    $response = get('/submissions/managing_submissions/filters');

    $response->assertOk();
    // When viewing a subfolder page, navigation should show the subfolder items
    $content = $response->getContent();
    expect($content)->toContain('Managing Submissions');
    expect($content)->toContain('/submissions/managing_submissions/filters');
});

it('generates clean URLs for leaf-folder documents in search index', function () {
    // Run the index builder
    $this->artisan('command:build-index')->assertExitCode(0);

    // Check that the filters document has a clean URL (folder name, not filename)
    $doc = \App\Models\Documentation::where('slug', 'like', '%filters%')
        ->where('url', 'like', '%managing_submissions%')
        ->first();

    expect($doc)->not->toBeNull();
    // URL should use folder name, not file name for leaf folders
    expect($doc->url)->toBe('/submissions/managing_submissions/filters');
});

describe('Raw markdown for LLMs', function () {
    it('returns raw markdown for root level pages with .md extension', function () {
        $response = get('/security.md');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');

        // Should contain raw markdown, not HTML
        $content = $response->getContent();
        expect($content)->toContain('# Security');
        expect($content)->not->toContain('<!DOCTYPE html>');
        expect($content)->not->toContain('<html');
    });

    it('returns raw markdown for nested folder pages with .md extension', function () {
        $response = get('/forms/overview.md');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');

        // Should contain raw markdown, not HTML
        $content = $response->getContent();
        expect($content)->toContain('# Forms Overview');
        expect($content)->not->toContain('<!DOCTYPE html>');
        expect($content)->not->toContain('<html');
    });

    it('returns raw markdown for index page', function () {
        $response = get('/index.md');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');

        // Should contain raw markdown content
        $content = $response->getContent();
        expect($content)->not->toContain('<!DOCTYPE html>');
        expect($content)->not->toContain('<html');
    });

    it('strips frontmatter from raw markdown', function () {
        $response = get('/security.md');

        $response->assertOk();
        $content = $response->getContent();

        // Should NOT contain YAML frontmatter markers
        expect($content)->not->toMatch('/^---\s*\n/');
    });

    it('returns 404 for non-existent raw markdown files', function () {
        $response = get('/non-existent-page.md');
        $response->assertNotFound();

        $response = get('/forms/non-existent.md');
        $response->assertNotFound();
    });

    it('returns raw markdown for 3-segment paths with .md extension', function () {
        $response = get('/submissions/managing_submissions/managing_submissions.md');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');

        $content = $response->getContent();
        expect($content)->not->toContain('<!DOCTYPE html>');
    });
});
