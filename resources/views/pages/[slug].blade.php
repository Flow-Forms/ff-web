<?php
use App\Helpers\MarkdownHelper;

// Check if the markdown file exists
if (!MarkdownHelper::markdownExists($slug)) {
    abort(404, 'Documentation page not found');
}

$content = MarkdownHelper::parseFile(resource_path("markdown/{$slug}.md"));
?>

@extends('layouts.docs')

@section('docs-content')
<div class="max-w-4xl mx-auto prose prose-lg">
    {!! $content !!}
</div>
@endsection