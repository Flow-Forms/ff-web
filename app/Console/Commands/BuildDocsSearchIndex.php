<?php

namespace App\Console\Commands;

use App\Helpers\MarkdownHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;

class BuildDocsSearchIndex extends Command
{
    protected $signature = 'command:build-index';
    protected $description = 'Build search index for documentation';

    private MarkdownConverter $converter;

    public function __construct()
    {
        parent::__construct();
        
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new FrontMatterExtension());
        
        $this->converter = new MarkdownConverter($environment);
    }

    public function handle()
    {
        $this->info('Building documentation search index...');
        
        $markdownPath = resource_path('markdown');
        $searchIndex = [];
        
        // Process all markdown files
        $this->processDirectory($markdownPath, $searchIndex);
        
        // Save the index
        $indexPath = public_path('docs-search-index.json');
        File::put($indexPath, json_encode($searchIndex, JSON_PRETTY_PRINT));
        
        $this->info('Search index built successfully!');
        $this->info('Index saved to: ' . $indexPath);
        $this->info('Total documents indexed: ' . count($searchIndex));
    }
    
    private function processDirectory(string $path, array &$searchIndex, string $basePath = ''): void
    {
        $files = File::files($path);
        $directories = File::directories($path);
        
        // Process markdown files
        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $this->processMarkdownFile($file->getPathname(), $searchIndex, $basePath);
            }
        }
        
        // Process subdirectories
        foreach ($directories as $directory) {
            $folderName = basename($directory);
            $this->processDirectory($directory, $searchIndex, $basePath . $folderName . '/');
        }
    }
    
    private function processMarkdownFile(string $filePath, array &$searchIndex, string $basePath): void
    {
        $content = File::get($filePath);
        $filename = pathinfo($filePath, PATHINFO_FILENAME);
        
        // Parse markdown to extract text content
        $result = $this->converter->convert($content);
        $htmlContent = $result->getContent();
        
        // Extract text from HTML
        $textContent = strip_tags($htmlContent);
        $textContent = html_entity_decode($textContent, ENT_QUOTES | ENT_HTML5);
        $textContent = preg_replace('/\s+/', ' ', $textContent);
        $textContent = trim($textContent);
        
        // Extract headings for better search relevance
        $headings = $this->extractHeadings($htmlContent);
        
        // Remove numeric prefix from filename for URL
        $cleanFilename = preg_replace('/^\d{2}-/', '', $filename);
        
        // Build URL
        $url = rtrim('/' . $basePath . $cleanFilename, '/');
        if ($url === '/index') {
            $url = '/';
        }
        
        // Create search entry
        $searchEntry = [
            'id' => $basePath . $cleanFilename,
            'title' => MarkdownHelper::filenameToTitle($filename),
            'content' => $this->truncateContent($textContent, 500),
            'headings' => implode(' ', $headings),
            'url' => $url,
            'section' => $basePath ? MarkdownHelper::filenameToTitle(rtrim($basePath, '/')) : 'Documentation',
            'breadcrumb' => $this->generateBreadcrumb($basePath, $filename),
        ];
        
        $searchIndex[] = $searchEntry;
        
        $this->line('  Indexed: ' . $basePath . $filename);
    }
    
    private function extractHeadings(string $html): array
    {
        $headings = [];
        
        // Extract h1-h3 headings
        preg_match_all('/<h[1-3][^>]*>(.*?)<\/h[1-3]>/i', $html, $matches);
        
        foreach ($matches[1] as $heading) {
            $headings[] = strip_tags($heading);
        }
        
        return $headings;
    }
    
    private function truncateContent(string $content, int $length): string
    {
        if (strlen($content) <= $length) {
            return $content;
        }
        
        $truncated = substr($content, 0, $length);
        $lastSpace = strrpos($truncated, ' ');
        
        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }
        
        return $truncated . '...';
    }
    
    private function generateBreadcrumb(string $basePath, string $filename): string
    {
        $parts = [];
        
        if ($basePath) {
            $folders = explode('/', rtrim($basePath, '/'));
            foreach ($folders as $folder) {
                $parts[] = MarkdownHelper::filenameToTitle($folder);
            }
        }
        
        $parts[] = MarkdownHelper::filenameToTitle($filename);
        
        return implode(' › ', $parts);
    }
}