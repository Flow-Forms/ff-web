<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Documentation extends Model
{
    use Searchable;

    protected $table = 'documentation';

    protected $fillable = [
        'slug',
        'title',
        'content',
        'headings',
        'section',
        'breadcrumb',
        'url',
        'file_path',
    ];

    protected $casts = [
        'headings' => 'array',
    ];

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'content' => $this->content,
            'headings' => is_array($this->headings) ? implode(' ', $this->headings) : $this->headings,
            'section' => $this->section,
            'breadcrumb' => $this->breadcrumb,
            'url' => $this->url,
        ];
    }
}