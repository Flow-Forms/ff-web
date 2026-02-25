<?php

use App\Helpers\LaravelSlugNormalizer;

it('slugifies plain text', function () {
    $normalizer = new LaravelSlugNormalizer;

    expect($normalizer->normalize('Reassign'))->toBe('reassign');
    expect($normalizer->normalize('Add Note'))->toBe('add-note');
    expect($normalizer->normalize('Download as PDF'))->toBe('download-as-pdf');
});

it('strips icon shortcodes before slugifying', function () {
    $normalizer = new LaravelSlugNormalizer;

    expect($normalizer->normalize('{{icon:arrow-path-rounded-square size-4 inline-block align-text-center}} Reassign'))
        ->toBe('reassign');

    expect($normalizer->normalize('{{icon:cog-6-tooth size-15 inline-block align-text-center}} Options'))
        ->toBe('options');

    expect($normalizer->normalize('{{icon:share size-6 inline-block align-text-center}} Share'))
        ->toBe('share');
});

it('strips icon shortcodes without extra classes', function () {
    $normalizer = new LaravelSlugNormalizer;

    expect($normalizer->normalize('{{icon:star}} Favorites'))->toBe('favorites');
});

it('returns empty string for icon-only heading', function () {
    $normalizer = new LaravelSlugNormalizer;

    expect($normalizer->normalize('{{icon:star size-6}}'))->toBe('');
});
