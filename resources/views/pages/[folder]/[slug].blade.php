<?php
use App\Helpers\MarkdownHelper;

// Check if the markdown file exists in the folder
if (!MarkdownHelper::markdownExists($slug, $folder)) {
    abort(404, 'Documentation page not found');
}

$content = MarkdownHelper::parseFile(MarkdownHelper::getMarkdownPath($slug, $folder));
?>

@extends('layouts.docs')

@section('docs-content')
<div class="max-w-4xl mx-auto prose prose-lg">
    {!! $content !!}
</div>
@endsection