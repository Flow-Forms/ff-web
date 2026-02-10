<?php
use App\Helpers\MarkdownHelper;
use function Laravel\Folio\{name, render};

name('docs.subfolder.show');

render(function (\Illuminate\View\View $view, ?string $folder = null, ?string $subfolder = null, ?string $slug = null) {
    if ($folder === null || $subfolder === null || $slug === null) {
        abort(404, 'Documentation page not found');
    }

    $filePath = MarkdownHelper::resolveMarkdownPath($folder, $subfolder, $slug);

    if (!$filePath) {
        abort(404, 'Documentation page not found');
    }

    $data = MarkdownHelper::parseWithFrontmatter($filePath);
    $frontmatter = $data['frontmatter'];
    $content = $data['html'];

    $pageTitle = $frontmatter['title'] ?? MarkdownHelper::filenameToTitle($slug);
    $sectionTitle = MarkdownHelper::filenameToTitle($folder);
    $title = $pageTitle . ' - ' . $sectionTitle . ' - Flow Forms Documentation';

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
