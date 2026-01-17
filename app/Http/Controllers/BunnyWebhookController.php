<?php

namespace App\Http\Controllers;

use App\Enums\VideoStatus;
use App\Http\Requests\BunnyWebhookRequest;
use App\Jobs\SyncVideoTranscription;
use App\Jobs\TriggerVideoTranscription;
use App\Models\Video;
use App\Services\BunnyStreamService;
use Illuminate\Http\JsonResponse;

class BunnyWebhookController extends Controller
{
    /**
     * Bunny Stream webhook event types.
     */
    private const EVENT_CAPTIONS_GENERATED = 9;

    private const EVENT_TITLE_DESCRIPTION_GENERATED = 10;

    public function __construct(
        private BunnyStreamService $bunnyService
    ) {}

    public function __invoke(BunnyWebhookRequest $request): JsonResponse
    {
        $videoId = $request->validated('VideoId');
        $status = $request->validated('Status');

        if ($status === null) {
            return response()->json(['error' => 'Missing status'], 400);
        }

        $status = (int) $status;

        $video = Video::where('bunny_video_id', $videoId)->first();

        if (! $video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        // Handle transcription-related events
        if ($this->isTranscriptionEvent($status)) {
            return $this->handleTranscriptionEvent($video);
        }

        // Handle video processing status events
        return $this->handleVideoStatusEvent($video, $videoId, $status);
    }

    private function isTranscriptionEvent(int $status): bool
    {
        return in_array($status, [
            self::EVENT_CAPTIONS_GENERATED,
            self::EVENT_TITLE_DESCRIPTION_GENERATED,
        ]);
    }

    private function handleTranscriptionEvent(Video $video): JsonResponse
    {
        // Dispatch job to sync transcription data from Bunny
        SyncVideoTranscription::dispatch($video);

        return response()->json(['success' => true, 'event' => 'transcription']);
    }

    private function handleVideoStatusEvent(Video $video, string $videoId, int $status): JsonResponse
    {
        $newStatus = $this->bunnyService->mapStatus($status);

        $updateData = ['status' => $newStatus];

        if ($newStatus === VideoStatus::Ready) {
            $bunnyVideo = $this->bunnyService->getVideo($videoId);
            if ($bunnyVideo) {
                $updateData['duration_seconds'] = $bunnyVideo['length'] ?? null;
                $updateData['thumbnail_url'] = $this->bunnyService->getThumbnailUrl($videoId);
            }

            // Trigger transcription when video finishes processing
            TriggerVideoTranscription::dispatch($video);
        }

        $video->update($updateData);

        return response()->json(['success' => true]);
    }
}
