<?php
use App\Helpers\MarkdownHelper;

// Check if the markdown file exists in the folder
if (!MarkdownHelper::markdownExists($slug, $folder)) {
    abort(404, 'Documentation page not found');
}

$data = MarkdownHelper::parseWithFrontmatter(MarkdownHelper::getMarkdownPath($slug, $folder));
$frontmatter = $data['frontmatter'];
$content = $data['html'];

// Use frontmatter title if available, otherwise use filename
$pageTitle = $frontmatter['title'] ?? MarkdownHelper::filenameToTitle($slug);
$sectionTitle = MarkdownHelper::filenameToTitle($folder);
$title = $pageTitle . ' - ' . $sectionTitle . ' - Flow Forms Documentation';
?>

<x-layouts.docs :title="$title">
    <div class="max-w-4xl mx-auto prose prose-lg dark:prose-invert prose-gray">
        {!! $content !!}
    </div>
</x-layouts.docs>