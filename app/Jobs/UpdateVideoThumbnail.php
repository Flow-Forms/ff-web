<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\BunnyStreamService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdateVideoThumbnail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [30, 60, 120];

    public function __construct(
        public Video $video,
        public string $thumbnailPath,
    ) {}

    public function handle(BunnyStreamService $bunny): void
    {
        if (! $this->video->bunny_video_id) {
            Log::warning('Cannot update thumbnail for video without bunny_video_id', [
                'video_id' => $this->video->id,
            ]);
            $this->cleanupThumbnail();

            return;
        }

        $thumbnailUrl = Storage::disk('s3')->url($this->thumbnailPath);
        $success = $bunny->setThumbnail($this->video->bunny_video_id, $thumbnailUrl);

        if ($success) {
            $this->video->update([
                'thumbnail_url' => $bunny->getThumbnailUrl($this->video->bunny_video_id),
            ]);

            Log::info('Video thumbnail updated successfully', [
                'video_id' => $this->video->id,
                'bunny_video_id' => $this->video->bunny_video_id,
            ]);
        } else {
            Log::error('Failed to update video thumbnail', [
                'video_id' => $this->video->id,
                'bunny_video_id' => $this->video->bunny_video_id,
                'thumbnail_url' => $thumbnailUrl,
            ]);

            // Only cleanup if this is the final attempt
            if ($this->attempts() >= $this->tries) {
                $this->cleanupThumbnail();
            }

            $this->fail();
        }
    }

    public function failed(?\Throwable $exception): void
    {
        Log::error('UpdateVideoThumbnail job failed permanently', [
            'video_id' => $this->video->id,
            'thumbnail_path' => $this->thumbnailPath,
            'exception' => $exception?->getMessage(),
        ]);

        $this->cleanupThumbnail();
    }

    private function cleanupThumbnail(): void
    {
        if (Storage::disk('s3')->exists($this->thumbnailPath)) {
            Storage::disk('s3')->delete($this->thumbnailPath);
        }
    }
}
