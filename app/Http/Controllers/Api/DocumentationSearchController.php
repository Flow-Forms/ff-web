<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use Illuminate\Http\Request;

class DocumentationSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'results' => [],
                'query' => $query,
                'total' => 0,
            ]);
        }

        // Perform search using Scout/Typesense
        $results = Documentation::search($query)
            ->take(20)
            ->get()
            ->map(function ($doc) use ($query) {
                return [
                    'id' => $doc->slug,
                    'title' => $doc->title,
                    'content' => $this->getSnippet($doc->content, $query),
                    'section' => $doc->section,
                    'breadcrumb' => $doc->breadcrumb,
                    'url' => $doc->url,
                ];
            });

        // Group results by section
        $groupedResults = $results->groupBy('section')->map(function ($items, $section) {
            return [
                'section' => $section,
                'items' => $items->values()->toArray(),
            ];
        })->values();

        return response()->json([
            'results' => $groupedResults,
            'query' => $query,
            'total' => $results->count(),
        ]);
    }

    /**
     * Get a snippet of content around the search term
     */
    private function getSnippet(string $content, string $query, int $snippetLength = 200): string
    {
        $queryLower = strtolower($query);
        $contentLower = strtolower($content);
        
        // Find the position of the search term
        $position = strpos($contentLower, $queryLower);
        
        if ($position === false) {
            // If not found, return the beginning of the content
            return strlen($content) > $snippetLength 
                ? substr($content, 0, $snippetLength) . '...'
                : $content;
        }
        
        // Calculate snippet start position
        $start = max(0, $position - ($snippetLength / 2));
        $snippet = substr($content, $start, $snippetLength);
        
        // Clean up snippet boundaries
        if ($start > 0) {
            $firstSpace = strpos($snippet, ' ');
            if ($firstSpace !== false) {
                $snippet = substr($snippet, $firstSpace + 1);
            }
            $snippet = '...' . $snippet;
        }
        
        if (strlen($content) > $start + $snippetLength) {
            $lastSpace = strrpos($snippet, ' ');
            if ($lastSpace !== false) {
                $snippet = substr($snippet, 0, $lastSpace);
            }
            $snippet .= '...';
        }
        
        return $snippet;
    }
}