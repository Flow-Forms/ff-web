<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use League\CommonMark\Normalizer\TextNormalizerInterface;

/**
 * Custom slug normalizer that uses Laravel's Str::slug() to ensure
 * heading IDs match the anchor slugs generated for internal links.
 */
class LaravelSlugNormalizer implements TextNormalizerInterface
{
    public function normalize(string $text, ?array $context = null): string
    {
        return Str::slug($text);
    }
}
