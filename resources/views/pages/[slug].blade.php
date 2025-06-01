<?php
use App\Helpers\MarkdownHelper;

// Check if the markdown file exists
if (!MarkdownHelper::markdownExists($slug)) {
    abort(404, 'Documentation page not found');
}

$content = MarkdownHelper::parseFile(MarkdownHelper::getMarkdownPath($slug));
$title = MarkdownHelper::filenameToTitle($slug) . ' - Flow Forms Documentation';
?>

<x-layouts.docs :title="$title">
    <div class="max-w-4xl mx-auto prose prose-lg dark:prose-invert prose-gray">
        {!! $content !!}
    </div>
</x-layouts.docs>