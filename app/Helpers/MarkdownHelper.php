<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;
use Symfony\Component\Yaml\Yaml;

class MarkdownHelper
{
    public static function parseFile(string $filePath): string
    {
        $data = self::parseWithFrontmatter($filePath);

        return $data['html'];
    }

    public static function parse(string $markdown): string
    {
        // Normalize standard markdown links for internal navigation
        // - Same-page anchors: [text](#Header Name) → [text](#header-name)
        // - Internal links: [text](path/to/page) → [text](/path/to/page)
        // - Internal with anchor: [text](path#Header) → [text](/path#header)
        // - External links (http/https/mailto): left unchanged
        $markdown = preg_replace_callback(
            '/\[([^\]]+)\]\(([^)]+)\)/',
            function ($matches) {
                $text = $matches[1];
                $url = $matches[2];

                // Leave external links unchanged
                if (preg_match('/^(https?:|mailto:|tel:)/i', $url)) {
                    return $matches[0];
                }

                // Parse the URL into path and anchor parts
                $anchor = '';
                if (str_contains($url, '#')) {
                    [$path, $anchor] = explode('#', $url, 2);
                    // Decode URL-encoded anchor (e.g., %20 → space) then slugify
                    $anchor = Str::slug(urldecode($anchor));
                } else {
                    $path = $url;
                }

                // Build the normalized URL
                if ($path !== '') {
                    // Internal page link - ensure leading slash
                    $path = ltrim($path, '/');
                    $url = '/'.$path;
                } else {
                    // Same-page anchor only
                    $url = '';
                }

                // Add the slugified anchor
                if ($anchor !== '') {
                    $url .= '#'.$anchor;
                }

                return '['.$text.']('.$url.')';
            },
            $markdown
        );

        $environment = new Environment([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
            'heading_permalink' => [
                'html_class' => 'heading-permalink',
                'id_prefix' => '',
                'apply_id_to_heading' => true,
                'heading_class' => '',
                'fragment_prefix' => '',
                'insert' => 'none', // Don't insert permalink symbol, just add the ID
                'min_heading_level' => 1,
                'max_heading_level' => 6,
                'title' => '',
                'symbol' => '',
                'aria_hidden' => true,
            ],
            // Use Laravel's Str::slug() for heading IDs to match anchor link normalization
            'slug_normalizer' => [
                'instance' => new LaravelSlugNormalizer,
            ],
        ]);

        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new GithubFlavoredMarkdownExtension);
        $environment->addExtension(new HeadingPermalinkExtension);

        $converter = new MarkdownConverter($environment);

        $html = $converter->convert($markdown)->getContent();

        // Replace icon shortcodes with rendered Flux icon components
        // Syntax: {{icon:icon-name}} or {{icon:icon-name size-6 text-blue-500}}
        $html = preg_replace_callback(
            '/\{\{icon:([a-z0-9-]+)(?:\s+([^\}]+))?\}\}/',
            function ($matches) {
                $icon = $matches[1];
                $classes = $matches[2] ?? 'size-5';

                // Check if icon exists in published views first
                $publishedPath = resource_path('views/flux/icon/'.$icon.'.blade.php');
                $vendorPath = base_path('vendor/livewire/flux/stubs/resources/views/flux/icon/'.$icon.'.blade.php');

                $iconPath = File::exists($publishedPath) ? $publishedPath : $vendorPath;

                if (! File::exists($iconPath)) {
                    return '<span class="text-red-500" title="Icon not found: '.$icon.'">[icon:'.$icon.']</span>';
                }

                try {
                    // Render the icon with the specified classes
                    $rendered = app('view')->file($iconPath, [
                        'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => $classes]),
                        'variant' => 'outline',
                    ])->render();

                    // Remove newlines and extra whitespace to keep icon inline
                    return preg_replace('/>\s+</', '><', trim($rendered));
                } catch (\Exception $e) {
                    return '<span class="text-red-500" title="Error rendering icon: '.$e->getMessage().'">[icon:'.$icon.']</span>';
                }
            },
            $html
        );

        return $html;
    }

    /**
     * Extract frontmatter and content from raw file content
     */
    protected static function extractFrontmatter(string $content): array
    {
        if (preg_match('/^---\s*\n(.*?)\n---\s*(?:\n(.*))?$/s', $content, $matches)) {
            return [
                'frontmatter' => Yaml::parse($matches[1]),
                'content' => $matches[2] ?? '',
            ];
        }

        return [
            'frontmatter' => [],
            'content' => $content,
        ];
    }

    public static function parseWithFrontmatter(string $filePath): array
    {
        if (! File::exists($filePath)) {
            return [
                'content' => '',
                'frontmatter' => [],
                'html' => '<p>Documentation file not found.</p>',
            ];
        }

        $fileContent = File::get($filePath);
        $extracted = self::extractFrontmatter($fileContent);

        return [
            'content' => $extracted['content'],
            'frontmatter' => $extracted['frontmatter'],
            'html' => self::parse($extracted['content']),
        ];
    }

    public static function getNavigationItems(): array
    {
        $markdownPath = resource_path('markdown');

        if (! File::exists($markdownPath)) {
            return [];
        }

        $navigation = [];

        // Get root level files
        $files = File::files($markdownPath);
        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $filename = $file->getFilenameWithoutExtension();

                // Parse frontmatter to get title and order
                $data = self::parseWithFrontmatter($file->getPathname());
                $frontmatter = $data['frontmatter'];

                // Use frontmatter title if available, otherwise generate from filename
                $title = $frontmatter['title'] ?? self::filenameToTitle($filename);
                $order = $frontmatter['order'] ?? 999; // Default high number for unordered items

                $url = '/'.$filename;

                $navigation['_root'][] = [
                    'title' => $title,
                    'url' => $url,
                    'filename' => $filename,
                    'order' => $order,
                    'type' => 'file',
                ];
            }
        }

        // Get directories and their files
        $directories = File::directories($markdownPath);
        foreach ($directories as $directory) {
            $folderName = basename($directory);

            // Check if folder has a _meta.md file for folder configuration
            $folderMetaPath = $directory.'/_meta.md';
            $folderOrder = 999;
            $folderTitle = self::filenameToTitle($folderName);

            if (File::exists($folderMetaPath)) {
                $folderMeta = self::parseWithFrontmatter($folderMetaPath);
                $folderTitle = $folderMeta['frontmatter']['title'] ?? $folderTitle;
                $folderOrder = $folderMeta['frontmatter']['order'] ?? 999;
            }

            $folderFiles = File::files($directory);
            $folderItems = [];

            // Collect direct .md files in this folder
            foreach ($folderFiles as $file) {
                if ($file->getExtension() === 'md' && $file->getFilename() !== '_meta.md') {
                    $filename = $file->getFilenameWithoutExtension();

                    // Parse frontmatter to get title and order
                    $data = self::parseWithFrontmatter($file->getPathname());
                    $frontmatter = $data['frontmatter'];

                    // Use frontmatter title if available
                    $title = $frontmatter['title'] ?? self::filenameToTitle($filename);
                    $order = $frontmatter['order'] ?? 999;

                    $url = '/'.$folderName.'/'.$filename;

                    $folderItems[] = [
                        'title' => $title,
                        'url' => $url,
                        'filename' => $filename,
                        'folder' => $folderName,
                        'order' => $order,
                        'type' => 'file',
                    ];
                }
            }

            // Scan for sub-directories within this folder
            $subDirectories = File::directories($directory);
            foreach ($subDirectories as $subDirectory) {
                $subfolderName = basename($subDirectory);

                // Read subfolder _meta.md for title/order
                $subMetaPath = $subDirectory.'/_meta.md';
                $subfolderOrder = 999;
                $subfolderTitle = self::filenameToTitle($subfolderName);

                if (File::exists($subMetaPath)) {
                    $subMeta = self::parseWithFrontmatter($subMetaPath);
                    $subfolderTitle = $subMeta['frontmatter']['title'] ?? $subfolderTitle;
                    $subfolderOrder = $subMeta['frontmatter']['order'] ?? 999;
                }

                $subfolderItems = [];

                // Collect direct .md files in the subfolder
                $subFiles = File::files($subDirectory);
                foreach ($subFiles as $subFile) {
                    if ($subFile->getExtension() === 'md' && $subFile->getFilename() !== '_meta.md') {
                        $subFilename = $subFile->getFilenameWithoutExtension();

                        $subData = self::parseWithFrontmatter($subFile->getPathname());
                        $subFrontmatter = $subData['frontmatter'];

                        $subFileTitle = $subFrontmatter['title'] ?? self::filenameToTitle($subFilename);
                        $subFileOrder = $subFrontmatter['order'] ?? 999;

                        $url = '/'.$folderName.'/'.$subfolderName.'/'.$subFilename;

                        $subfolderItems[] = [
                            'title' => $subFileTitle,
                            'url' => $url,
                            'filename' => $subFilename,
                            'folder' => $folderName,
                            'subfolder' => $subfolderName,
                            'order' => $subFileOrder,
                            'type' => 'file',
                            'is_leaf_folder' => false,
                        ];
                    }
                }

                // Collect leaf-folder files (sub-directories containing a single .md file besides _meta.md)
                $leafDirectories = File::directories($subDirectory);
                foreach ($leafDirectories as $leafDirectory) {
                    // Only treat as a leaf folder if it contains exactly 1 .md file besides _meta.md
                    $leafMdFiles = collect(File::files($leafDirectory))
                        ->filter(fn ($f) => $f->getExtension() === 'md' && $f->getFilename() !== '_meta.md');

                    if ($leafMdFiles->count() !== 1) {
                        continue;
                    }

                    $leafFolderName = basename($leafDirectory);

                    // Read leaf folder _meta.md for title/order
                    $leafMetaPath = $leafDirectory.'/_meta.md';
                    $leafOrder = 999;
                    $leafTitle = self::filenameToTitle($leafFolderName);

                    if (File::exists($leafMetaPath)) {
                        $leafMeta = self::parseWithFrontmatter($leafMetaPath);
                        $leafTitle = $leafMeta['frontmatter']['title'] ?? $leafTitle;
                        $leafOrder = $leafMeta['frontmatter']['order'] ?? 999;
                    }

                    // Find the single .md file in the leaf folder (besides _meta.md)
                    $leafFiles = File::files($leafDirectory);
                    foreach ($leafFiles as $leafFile) {
                        if ($leafFile->getExtension() === 'md' && $leafFile->getFilename() !== '_meta.md') {
                            // Use _meta.md title/order if available, otherwise fall back to folder name
                            $leafFileTitle = $leafTitle;
                            $leafFileOrder = $leafOrder;

                            // Clean URL uses the leaf folder name as the slug
                            $url = '/'.$folderName.'/'.$subfolderName.'/'.$leafFolderName;

                            $subfolderItems[] = [
                                'title' => $leafFileTitle,
                                'url' => $url,
                                'filename' => $leafFolderName,
                                'folder' => $folderName,
                                'subfolder' => $subfolderName,
                                'order' => $leafFileOrder,
                                'type' => 'file',
                                'is_leaf_folder' => true,
                            ];
                        }
                    }
                }

                if (! empty($subfolderItems)) {
                    // Sort subfolder items by order, then by title
                    usort($subfolderItems, function ($a, $b) {
                        if ($a['order'] == $b['order']) {
                            return strcmp($a['title'], $b['title']);
                        }

                        return $a['order'] - $b['order'];
                    });

                    $folderItems[] = [
                        'title' => $subfolderTitle,
                        'type' => 'subfolder',
                        'order' => $subfolderOrder,
                        'folder' => $folderName,
                        'subfolder' => $subfolderName,
                        'items' => $subfolderItems,
                    ];
                }
            }

            if (! empty($folderItems)) {
                // Sort files and subfolders within folder by order, then by title
                usort($folderItems, function ($a, $b) {
                    if ($a['order'] == $b['order']) {
                        return strcmp($a['title'], $b['title']);
                    }

                    return $a['order'] - $b['order'];
                });

                $navigation[$folderName] = [
                    'title' => $folderTitle,
                    'type' => 'folder',
                    'order' => $folderOrder,
                    'items' => $folderItems,
                ];
            }
        }

        // Sort root files by order, then by title
        if (isset($navigation['_root'])) {
            usort($navigation['_root'], function ($a, $b) {
                if ($a['order'] == $b['order']) {
                    return strcmp($a['title'], $b['title']);
                }

                return $a['order'] - $b['order'];
            });
        }

        // Convert root files to folder-like structure for unified sorting
        $allItems = [];

        if (isset($navigation['_root'])) {
            foreach ($navigation['_root'] as $file) {
                $allItems[$file['filename']] = [
                    'title' => $file['title'],
                    'type' => 'file',
                    'order' => $file['order'],
                    'url' => $file['url'],
                    'filename' => $file['filename'],
                ];
            }
            unset($navigation['_root']);
        }

        // Add folders
        foreach ($navigation as $key => $folder) {
            $allItems[$key] = $folder;
        }

        // Sort everything by order
        uasort($allItems, function ($a, $b) {
            if ($a['order'] == $b['order']) {
                return strcmp($a['title'], $b['title']);
            }

            return $a['order'] - $b['order'];
        });

        return $allItems;
    }

    public static function filenameToTitle(string $filename): string
    {
        // Convert kebab-case and snake_case to title case
        return Str::of($filename)
            ->replace(['-', '_'], ' ')
            ->title()
            ->toString();
    }

    public static function markdownExists(string $filename, ?string $folder = null): bool
    {
        if ($folder) {
            $filePath = resource_path("markdown/$folder/$filename.md");
        } else {
            $filePath = resource_path("markdown/$filename.md");
        }

        return File::exists($filePath);
    }

    public static function getMarkdownPath(string $filename, ?string $folder = null): string
    {
        if ($folder) {
            return resource_path("markdown/$folder/$filename.md");
        } else {
            return resource_path("markdown/$filename.md");
        }
    }

    public static function getRawContent(string $filename, ?string $folder = null): string
    {
        return self::getRawContentFromPath(self::getMarkdownPath($filename, $folder));
    }

    public static function getRawContentFromPath(string $filePath): string
    {
        if (! File::exists($filePath)) {
            return '';
        }

        $fileContent = File::get($filePath);
        $extracted = self::extractFrontmatter($fileContent);

        return $extracted['content'];
    }

    public static function getIndexDocumentPath(): ?string
    {
        $markdownPath = resource_path('markdown');

        if (! File::exists($markdownPath)) {
            return null;
        }

        // Look for a file with is_index: true in frontmatter
        $files = File::files($markdownPath);
        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $data = self::parseWithFrontmatter($file->getPathname());
                if (isset($data['frontmatter']['is_index']) && $data['frontmatter']['is_index'] === true) {
                    return $file->getPathname();
                }
            }
        }

        // Fallback to looking for overview.md or index.md
        $fallbackFiles = ['overview.md', 'index.md'];
        foreach ($fallbackFiles as $filename) {
            $path = resource_path("markdown/$filename");
            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
