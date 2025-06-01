<?php
use App\Helpers\MarkdownHelper;

// Check if the markdown file exists in the folder
if (!MarkdownHelper::markdownExists($slug, $folder)) {
    abort(404, 'Documentation page not found');
}

$content = MarkdownHelper::parseFile(MarkdownHelper::getMarkdownPath($slug, $folder));
$title = MarkdownHelper::filenameToTitle($slug) . ' - ' . MarkdownHelper::filenameToTitle($folder) . ' - Flow Forms Documentation';
?>

<x-layouts.docs :title="$title">
    <div class="max-w-4xl mx-auto prose prose-lg dark:prose-invert prose-gray">
        {!! $content !!}
    </div>
</x-layouts.docs>