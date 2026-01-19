<?php

namespace App\Jobs;

use App\Enums\TranscriptionStatus;
use App\Models\Video;
use App\Services\BunnyStreamService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class TriggerVideoTranscription implements ShouldQueue
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
            Log::warning('Cannot transcribe video without bunny_video_id', [
                'video_id' => $this->video->id,
            ]);

            return;
        }

        // Update status to processing
        $this->video->update([
            'transcription_status' => TranscriptionStatus::Processing,
        ]);

        $success = $bunny->transcribeVideo($this->video->bunny_video_id, [
            'language' => $this->video->transcript_language ?? 'en',
            'generateTitle' => true,
            'generateDescription' => true,
            'generateChapters' => true,
            'generateMoments' => true,
        ]);

        if (! $success) {
            Log::error('Failed to trigger transcription for video', [
                'video_id' => $this->video->id,
                'bunny_video_id' => $this->video->bunny_video_id,
            ]);

            $this->video->update([
                'transcription_status' => TranscriptionStatus::Failed,
            ]);
        }
    }
}
