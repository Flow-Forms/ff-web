<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use League\CommonMark\GithubFlavoredMarkdownConverter;
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
        // Syntax: {{icon:icon-name}} or {{icon:icon-name size-6 text-blue-500}}
        $markdown = preg_replace_callback(
            '/\{\{icon:([a-z0-9-]+)(?:\s+([^\}]+))?\}\}/',
            function ($matches) {
                $icon = $matches[1];
                $classes = $matches[2] ?? 'size-5';
                return "<flux:icon.$icon class=\"$classes\" />";
            },
            $markdown
        );

        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);

        return $converter->convert($markdown)->getContent();
    }
    
    /**
     * Extract frontmatter and content from raw file content
     */
    protected static function extractFrontmatter(string $content): array
    {
        if (preg_match('/^---\s*\n(.*?)\n---\s*(?:\n(.*))?$/s', $content, $matches)) {
            return [
                'frontmatter' => Yaml::parse($matches[1]),
                'content' => $matches[2] ?? ''
            ];
        }
        
        return [
            'frontmatter' => [],
            'content' => $content
        ];
    }

    public static function parseWithFrontmatter(string $filePath): array
    {
        if (!File::exists($filePath)) {
            return [
                'content' => '',
                'frontmatter' => [],
                'html' => '<p>Documentation file not found.</p>'
            ];
        }

        $fileContent = File::get($filePath);
        $extracted = self::extractFrontmatter($fileContent);
        
        return [
            'content' => $extracted['content'],
            'frontmatter' => $extracted['frontmatter'],
            'html' => self::parse($extracted['content'])
        ];
    }

    public static function getNavigationItems(): array
    {
        $markdownPath = resource_path('markdown');
        
        if (!File::exists($markdownPath)) {
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
                
                $url = '/' . $filename;
                
                $navigation['_root'][] = [
                    'title' => $title,
                    'url' => $url,
                    'filename' => $filename,
                    'order' => $order,
                    'type' => 'file'
                ];
            }
        }

        // Get directories and their files
        $directories = File::directories($markdownPath);
        foreach ($directories as $directory) {
            $folderName = basename($directory);
            
            // Check if folder has a _meta.md file for folder configuration
            $folderMetaPath = $directory . '/_meta.md';
            $folderOrder = 999;
            $folderTitle = self::filenameToTitle($folderName);
            
            if (File::exists($folderMetaPath)) {
                $folderMeta = self::parseWithFrontmatter($folderMetaPath);
                $folderTitle = $folderMeta['frontmatter']['title'] ?? $folderTitle;
                $folderOrder = $folderMeta['frontmatter']['order'] ?? 999;
            }
            
            $folderFiles = File::files($directory);
            $folderItems = [];
            
            foreach ($folderFiles as $file) {
                if ($file->getExtension() === 'md' && $file->getFilename() !== '_meta.md') {
                    $filename = $file->getFilenameWithoutExtension();
                    
                    // Parse frontmatter to get title and order
                    $data = self::parseWithFrontmatter($file->getPathname());
                    $frontmatter = $data['frontmatter'];
                    
                    // Use frontmatter title if available
                    $title = $frontmatter['title'] ?? self::filenameToTitle($filename);
                    $order = $frontmatter['order'] ?? 999;
                    
                    $url = '/' . $folderName . '/' . $filename;
                    
                    $folderItems[] = [
                        'title' => $title,
                        'url' => $url,
                        'filename' => $filename,
                        'folder' => $folderName,
                        'order' => $order,
                        'type' => 'file'
                    ];
                }
            }
            
            if (!empty($folderItems)) {
                // Sort files within folder by order, then by title
                usort($folderItems, function($a, $b) {
                    if ($a['order'] == $b['order']) {
                        return strcmp($a['title'], $b['title']);
                    }
                    return $a['order'] - $b['order'];
                });
                
                $navigation[$folderName] = [
                    'title' => $folderTitle,
                    'type' => 'folder',
                    'order' => $folderOrder,
                    'items' => $folderItems
                ];
            }
        }

        // Sort root files by order, then by title
        if (isset($navigation['_root'])) {
            usort($navigation['_root'], function($a, $b) {
                if ($a['order'] == $b['order']) {
                    return strcmp($a['title'], $b['title']);
                }
                return $a['order'] - $b['order'];
            });
        }
        
        // Sort folders by order
        $sortedNavigation = [];
        
        // Add root items first
        if (isset($navigation['_root'])) {
            $sortedNavigation['_root'] = $navigation['_root'];
            unset($navigation['_root']);
        }
        
        // Sort remaining folders by order
        uasort($navigation, function($a, $b) {
            return $a['order'] - $b['order'];
        });
        
        // Merge sorted folders back
        return array_merge($sortedNavigation, $navigation);
    }

    public static function filenameToTitle(string $filename): string
    {
        // Convert kebab-case and snake_case to title case
        return Str::of($filename)
            ->replace(['-', '_'], ' ')
            ->title()
            ->toString();
    }

    public static function markdownExists(string $filename, string $folder = null): bool
    {
        if ($folder) {
            $filePath = resource_path("markdown/$folder/$filename.md");
        } else {
            $filePath = resource_path("markdown/$filename.md");
        }
        
        return File::exists($filePath);
    }

    public static function getMarkdownPath(string $filename, string $folder = null): string
    {
        if ($folder) {
            return resource_path("markdown/$folder/$filename.md");
        } else {
            return resource_path("markdown/$filename.md");
        }
    }
    
    public static function getIndexDocumentPath(): ?string
    {
        $markdownPath = resource_path('markdown');
        
        if (!File::exists($markdownPath)) {
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