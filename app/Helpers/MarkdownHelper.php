<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;

class MarkdownHelper
{
    public static function parseFile(string $filePath): string
    {
        if (!File::exists($filePath)) {
            return '<p>Documentation file not found.</p>';
        }

        $markdown = File::get($filePath);
        
        return self::parse($markdown);
    }

    public static function parse(string $markdown): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return $converter->convert($markdown)->getContent();
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
                $title = self::filenameToTitle($filename);
                $url = '/' . $filename;

                // Store the original filename for sorting, but clean URL
                $cleanFilename = preg_replace('/^\d{2}-/', '', $filename);
                $url = '/' . $cleanFilename;
                
                $navigation['_root'][] = [
                    'title' => $title,
                    'url' => $url,
                    'filename' => $cleanFilename,
                    'originalFilename' => $filename,
                    'type' => 'file'
                ];
            }
        }

        // Get directories and their files
        $directories = File::directories($markdownPath);
        foreach ($directories as $directory) {
            $folderName = basename($directory);
            $folderTitle = self::filenameToTitle($folderName);
            
            $folderFiles = File::files($directory);
            $folderItems = [];
            
            foreach ($folderFiles as $file) {
                if ($file->getExtension() === 'md') {
                    $filename = $file->getFilenameWithoutExtension();
                    $title = self::filenameToTitle($filename);
                    $url = '/' . $folderName . '/' . $filename;

                    // Store the original filename for sorting, but clean URL
                    $cleanFilename = preg_replace('/^\d{2}-/', '', $filename);
                    $url = '/' . $folderName . '/' . $cleanFilename;
                    
                    $folderItems[] = [
                        'title' => $title,
                        'url' => $url,
                        'filename' => $cleanFilename,
                        'originalFilename' => $filename,
                        'folder' => $folderName,
                        'type' => 'file'
                    ];
                }
            }
            
            if (!empty($folderItems)) {
                // Sort files within folder by their original filename (preserves numeric ordering)
                usort($folderItems, function($a, $b) {
                    return strcmp($a['originalFilename'], $b['originalFilename']);
                });
                
                $navigation[$folderName] = [
                    'title' => $folderTitle,
                    'type' => 'folder',
                    'items' => $folderItems
                ];
            }
        }

        // Sort root files if they exist
        if (isset($navigation['_root'])) {
            usort($navigation['_root'], fn($a, $b) => strcmp($a['originalFilename'], $b['originalFilename']));
        }

        return $navigation;
    }

    public static function filenameToTitle(string $filename): string
    {
        // Remove numeric prefix if present (e.g., "01-overview" becomes "overview")
        $filename = preg_replace('/^\d{2}-/', '', $filename);
        
        // Convert kebab-case and snake_case to title case
        return Str::of($filename)
            ->replace(['-', '_'], ' ')
            ->title()
            ->toString();
    }

    public static function markdownExists(string $filename, string $folder = null): bool
    {
        // Check for exact filename first
        if ($folder) {
            $filePath = resource_path("markdown/{$folder}/{$filename}.md");
        } else {
            $filePath = resource_path("markdown/{$filename}.md");
        }
        
        if (File::exists($filePath)) {
            return true;
        }
        
        // Check for files with numeric prefix
        $directory = $folder ? resource_path("markdown/{$folder}") : resource_path("markdown");
        if (File::exists($directory)) {
            $files = File::files($directory);
            foreach ($files as $file) {
                if ($file->getExtension() === 'md') {
                    $baseFilename = $file->getFilenameWithoutExtension();
                    $cleanFilename = preg_replace('/^\d{2}-/', '', $baseFilename);
                    if ($cleanFilename === $filename) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }

    public static function getMarkdownPath(string $filename, string $folder = null): string
    {
        // Check for exact filename first
        if ($folder) {
            $filePath = resource_path("markdown/{$folder}/{$filename}.md");
        } else {
            $filePath = resource_path("markdown/{$filename}.md");
        }
        
        if (File::exists($filePath)) {
            return $filePath;
        }
        
        // Check for files with numeric prefix
        $directory = $folder ? resource_path("markdown/{$folder}") : resource_path("markdown");
        if (File::exists($directory)) {
            $files = File::files($directory);
            foreach ($files as $file) {
                if ($file->getExtension() === 'md') {
                    $baseFilename = $file->getFilenameWithoutExtension();
                    $cleanFilename = preg_replace('/^\d{2}-/', '', $baseFilename);
                    if ($cleanFilename === $filename) {
                        return $file->getPathname();
                    }
                }
            }
        }
        
        // Return the expected path even if it doesn't exist
        if ($folder) {
            return resource_path("markdown/{$folder}/{$filename}.md");
        } else {
            return resource_path("markdown/{$filename}.md");
        }
    }
}