<?php

namespace App\Http\Controllers;

use App\Enums\VideoStatus;
use App\Http\Requests\BunnyWebhookRequest;
use App\Models\Video;
use App\Services\BunnyStreamService;
use Illuminate\Http\JsonResponse;

class BunnyWebhookController extends Controller
{
    public function __construct(
        private BunnyStreamService $bunnyService
    ) {}

    public function __invoke(BunnyWebhookRequest $request): JsonResponse
    {
        $videoId = $request->validated('VideoId');
        $status = $request->validated('Status');

        $video = Video::where('bunny_video_id', $videoId)->first();

        if (! $video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        $newStatus = $this->bunnyService->mapStatus((int) $status);

        $updateData = ['status' => $newStatus];

        if ($newStatus === VideoStatus::Ready) {
            $bunnyVideo = $this->bunnyService->getVideo($videoId);
            if ($bunnyVideo) {
                $updateData['duration_seconds'] = $bunnyVideo['length'] ?? null;
                $updateData['thumbnail_url'] = $this->bunnyService->getThumbnailUrl($videoId);
            }
        }

        $video->update($updateData);

        return response()->json(['success' => true]);
    }
}
