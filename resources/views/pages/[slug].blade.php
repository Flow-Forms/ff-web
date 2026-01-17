<?php
use App\Helpers\MarkdownHelper;
use function Laravel\Folio\render;

render(function (\Illuminate\View\View $view, string $slug) {
    // Check if the markdown file exists
    if (!MarkdownHelper::markdownExists($slug)) {
        abort(404, 'Documentation page not found');
    }

    $data = MarkdownHelper::parseWithFrontmatter(MarkdownHelper::getMarkdownPath($slug));
    $frontmatter = $data['frontmatter'];
    $content = $data['html'];

    // Use frontmatter title if available, otherwise use filename
    $pageTitle = $frontmatter['title'] ?? MarkdownHelper::filenameToTitle($slug);
    $title = $pageTitle . ' - Flow Forms Documentation';

    return $view->with([
        'content' => $content,
        'title' => $title,
    ]);
});
?>

<x-layouts.app :title="$title">
    <div class="max-w-4xl mx-auto prose prose-lg dark:prose-invert prose-gray">
        {!! $content !!}
    </div>
</x-layouts.app>