<?php

namespace App\Models;

use App\Enums\TranscriptionStatus;
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
        'transcript',
        'transcript_language',
        'chapters',
        'moments',
        'transcription_status',
        'transcribed_at',
        'captions',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'duration_seconds' => 'integer',
            'order' => 'integer',
            'status' => VideoStatus::class,
            'chapters' => 'array',
            'moments' => 'array',
            'transcription_status' => TranscriptionStatus::class,
            'transcribed_at' => 'datetime',
            'captions' => 'array',
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

    public function isTranscribing(): bool
    {
        return $this->transcription_status === TranscriptionStatus::Processing;
    }

    public function isTranscribed(): bool
    {
        return $this->transcription_status === TranscriptionStatus::Completed;
    }

    public function needsTranscription(): bool
    {
        return $this->isReady()
            && $this->transcription_status === TranscriptionStatus::Pending;
    }

    public function hasChapters(): bool
    {
        return ! empty($this->chapters);
    }

    public function hasTranscript(): bool
    {
        return ! empty($this->transcript);
    }

    /**
     * Get the combined status label for display.
     * Shows video processing status until ready, then transcription status.
     */
    public function getCombinedStatusLabel(): string
    {
        if (! $this->isReady()) {
            return $this->status->label();
        }

        return match ($this->transcription_status) {
            TranscriptionStatus::Pending => 'Awaiting Transcription',
            TranscriptionStatus::Processing => 'Transcribing',
            TranscriptionStatus::Completed => 'Ready',
            TranscriptionStatus::Failed => 'Transcription Failed',
            default => 'Ready',
        };
    }

    /**
     * Get the combined status color for display.
     */
    public function getCombinedStatusColor(): string
    {
        if (! $this->isReady()) {
            return $this->status->color();
        }

        return match ($this->transcription_status) {
            TranscriptionStatus::Pending => 'zinc',
            TranscriptionStatus::Processing => 'yellow',
            TranscriptionStatus::Completed => 'green',
            TranscriptionStatus::Failed => 'red',
            default => 'green',
        };
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

    /**
     * Get description as inline HTML for excerpts/previews.
     * Converts block elements to spaces while preserving inline styling.
     */
    public function getDescriptionExcerpt(): ?string
    {
        if (! $this->description) {
            return null;
        }

        // Replace block elements with spaces
        $text = str_replace(
            ['<p>', '</p>', '<br>', '<br/>', '<br />', '<div>', '</div>'],
            ' ',
            $this->description
        );

        // Keep only inline styling tags
        $text = strip_tags($text, '<strong><em><b><i><u><a><span>');

        // Clean up multiple spaces
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * Get first 1-2 sentences of description as a teaser.
     */
    public function getSummaryTeaser(): ?string
    {
        if (! $this->description) {
            return null;
        }

        // Strip HTML and get plain text
        $text = strip_tags($this->description);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        if (empty($text)) {
            return null;
        }

        // Extract first 1-2 sentences (up to ~200 chars or 2 sentence endings)
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, 3, PREG_SPLIT_NO_EMPTY);

        if (count($sentences) <= 2) {
            return $text;
        }

        $teaser = $sentences[0];
        if (strlen($teaser) < 100 && isset($sentences[1])) {
            $teaser .= ' '.$sentences[1];
        }

        return $teaser;
    }

    /**
     * Check if the description has more content than the teaser.
     */
    public function hasSummaryBeyondTeaser(): bool
    {
        $teaser = $this->getSummaryTeaser();
        $full = strip_tags($this->description ?? '');
        $full = preg_replace('/\s+/', ' ', $full);

        return strlen(trim($full)) > strlen($teaser ?? '');
    }
}
