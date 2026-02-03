<?php

namespace App\Console\Commands;

use App\Helpers\MarkdownHelper;
use App\Models\Documentation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\MarkdownConverter;

class BuildDocsSearchIndex extends Command
{
    protected $signature = 'command:build-index';

    protected $description = 'Build search index for documentation';

    private MarkdownConverter $converter;

    public function __construct()
    {
        parent::__construct();

        $environment = new Environment;
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new FrontMatterExtension);

        $this->converter = new MarkdownConverter($environment);
    }

    public function handle()
    {
        $this->info('Building documentation search index...');

        // Clear existing documentation
        Documentation::truncate();

        $markdownPath = resource_path('markdown');
        $documentsIndexed = 0;

        // Process all markdown files and save to database
        $this->processDirectory($markdownPath, $documentsIndexed);

        // Trigger Scout indexing
        $this->info('Indexing documents with Typesense...');
        Documentation::makeAllSearchable();

        $this->info('Search index built successfully!');
        $this->info('Total documents indexed: '.$documentsIndexed);
    }

    private function processDirectory(string $path, int &$documentsIndexed, string $basePath = ''): void
    {
        $files = File::files($path);
        $directories = File::directories($path);

        // Process markdown files
        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $this->processMarkdownFile($file->getPathname(), $documentsIndexed, $basePath);
            }
        }

        // Process subdirectories
        foreach ($directories as $directory) {
            $folderName = basename($directory);
            $this->processDirectory($directory, $documentsIndexed, $basePath.$folderName.'/');
        }
    }

    private function processMarkdownFile(string $filePath, int &$documentsIndexed, string $basePath): void
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
        $url = rtrim('/'.$basePath.$cleanFilename, '/');

        // Clean URL for leaf-folder files: if this .md is the only content file
        // in its directory (depth 3+), use the folder name as the slug
        $fileDir = dirname($filePath);
        $markdownRoot = resource_path('markdown');
        $depth = substr_count(trim(str_replace($markdownRoot, '', $fileDir), '/'), '/');

        if ($depth >= 2 && MarkdownHelper::isLeafFolder($fileDir)) {
            // This is a leaf folder — use parent folder name as slug
            $url = rtrim('/'.$basePath, '/');
        }

        if ($url === '/index') {
            $url = '/';
        }

        // Create documentation record
        Documentation::create([
            'slug' => $basePath.$cleanFilename,
            'title' => MarkdownHelper::filenameToTitle($filename),
            'content' => $textContent, // Store full content, no truncation
            'headings' => $headings,
            'url' => $url,
            'section' => $basePath ? MarkdownHelper::filenameToTitle(rtrim($basePath, '/')) : 'Documentation',
            'breadcrumb' => $this->generateBreadcrumb($basePath, $filename),
            'file_path' => $filePath,
        ]);

        $documentsIndexed++;
        $this->line('  Indexed: '.$basePath.$filename);
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
