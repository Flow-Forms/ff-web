<?php
use App\Helpers\MarkdownHelper;

// Check if the markdown file exists
if (!MarkdownHelper::markdownExists($slug)) {
    abort(404, 'Documentation page not found');
}

$content = MarkdownHelper::parseFile(resource_path("markdown/{$slug}.md"));
$title = MarkdownHelper::filenameToTitle($slug) . ' - Flow Forms Documentation';
?>

<x-layouts.docs :title="$title">
    <div class="max-w-4xl mx-auto prose prose-lg">
        {!! $content !!}
    </div>
</x-layouts.docs>