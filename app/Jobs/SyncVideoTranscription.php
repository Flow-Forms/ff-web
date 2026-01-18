<?php

namespace App\Jobs;

use App\Enums\TranscriptionStatus;
use App\Models\Video;
use App\Services\BunnyStreamService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncVideoTranscription implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public Video $video
    ) {}

    public function handle(BunnyStreamService $bunny): void
    {
        if (! $this->video->bunny_video_id) {
            Log::warning('Cannot sync transcription for video without bunny_video_id', [
                'video_id' => $this->video->id,
            ]);

            return;
        }

        // Fetch video data from Bunny
        $bunnyVideo = $bunny->getVideo($this->video->bunny_video_id);

        if (! $bunnyVideo) {
            Log::error('Failed to fetch video data from Bunny', [
                'video_id' => $this->video->id,
                'bunny_video_id' => $this->video->bunny_video_id,
            ]);

            return;
        }

        $updateData = [
            'transcription_status' => TranscriptionStatus::Completed,
            'transcribed_at' => now(),
        ];

        // Update title if Bunny generated one and we have a placeholder
        if (! empty($bunnyVideo['title']) && $this->isPlaceholderTitle()) {
            $updateData['title'] = $bunnyVideo['title'];
            $updateData['slug'] = Video::generateUniqueSlug($bunnyVideo['title']);
        }

        // Update description if Bunny generated one and we don't have one
        $metaTags = $bunnyVideo['metaTags'] ?? [];
        if (is_array($metaTags)) {
            foreach ($metaTags as $tag) {
                if (is_array($tag) && ($tag['property'] ?? '') === 'description' && ! empty($tag['value'])) {
                    if (empty($this->video->description)) {
                        $updateData['description'] = $tag['value'];
                    }
                    break;
                }
            }
        }

        // Store chapters if available
        $chapters = $bunnyVideo['chapters'] ?? [];
        if (is_array($chapters) && ! empty($chapters)) {
            $updateData['chapters'] = array_map(fn ($chapter) => [
                'title' => $chapter['title'] ?? '',
                'start' => $chapter['start'] ?? 0,
                'end' => $chapter['end'] ?? 0,
            ], $chapters);
        }

        // Store moments if available
        $moments = $bunnyVideo['moments'] ?? [];
        if (is_array($moments) && ! empty($moments)) {
            $updateData['moments'] = array_map(fn ($moment) => [
                'label' => $moment['label'] ?? '',
                'timestamp' => $moment['timestamp'] ?? 0,
            ], $moments);
        }

        // Store available captions
        $captions = $bunnyVideo['captions'] ?? [];
        if (is_array($captions) && ! empty($captions)) {
            $updateData['captions'] = array_map(fn ($caption) => [
                'srclang' => $caption['srclang'] ?? 'en',
                'label' => $caption['label'] ?? 'English',
            ], $captions);
        }

        // Try to fetch and parse the transcript from VTT
        $language = $this->video->transcript_language ?? 'en';
        $vttContent = $bunny->getCaptionContent($this->video->bunny_video_id, $language);

        if ($vttContent) {
            $updateData['transcript'] = $bunny->parseVttToTranscript($vttContent);
        }

        $this->video->update($updateData);

        Log::info('Video transcription synced successfully', [
            'video_id' => $this->video->id,
            'has_transcript' => ! empty($updateData['transcript']),
            'has_chapters' => ! empty($updateData['chapters']),
        ]);
    }

    /**
     * Check if the current title is a placeholder (e.g., filename-based).
     */
    private function isPlaceholderTitle(): bool
    {
        $title = $this->video->title;

        // If title matches common placeholder patterns
        if (preg_match('/^(video|upload|untitled|new video)/i', $title)) {
            return true;
        }

        // If title looks like a filename (contains extension)
        if (preg_match('/\.(mp4|mov|webm|avi|mkv)$/i', $title)) {
            return true;
        }

        return false;
    }
}
