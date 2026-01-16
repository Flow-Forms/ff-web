<?php

namespace App\Models;

use App\Enums\VideoStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Video extends Model
{
    /** @use HasFactory<\Database\Factories\VideoFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'r2_path',
        'bunny_video_id',
        'bunny_library_id',
        'duration_seconds',
        'thumbnail_url',
        'status',
        'is_published',
        'published_at',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'duration_seconds' => 'integer',
            'order' => 'integer',
            'status' => VideoStatus::class,
        ];
    }

    public static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->where('status', VideoStatus::Ready);
    }

    public function scopeReady(Builder $query): Builder
    {
        return $query->where('status', VideoStatus::Ready);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isReady(): bool
    {
        return $this->status === VideoStatus::Ready;
    }

    public function isProcessing(): bool
    {
        return $this->status === VideoStatus::Processing;
    }

    public function getEmbedUrl(): ?string
    {
        if (! $this->bunny_video_id || ! $this->bunny_library_id) {
            return null;
        }

        return "https://iframe.mediadelivery.net/embed/{$this->bunny_library_id}/{$this->bunny_video_id}";
    }

    public function getFormattedDuration(): ?string
    {
        if (! $this->duration_seconds) {
            return null;
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
