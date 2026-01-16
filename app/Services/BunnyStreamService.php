<?php

namespace App\Services;

use App\Enums\VideoStatus;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class BunnyStreamService
{
    private string $apiKey;

    private string $libraryId;

    private string $cdnHostname;

    private string $baseUrl = 'https://video.bunnycdn.com/library';

    public function __construct()
    {
        $this->apiKey = config('services.bunny.api_key')
            ?? throw new \RuntimeException('BUNNY_API_KEY is not configured');
        $this->libraryId = config('services.bunny.library_id')
            ?? throw new \RuntimeException('BUNNY_LIBRARY_ID is not configured');
        $this->cdnHostname = config('services.bunny.cdn_hostname')
            ?? throw new \RuntimeException('BUNNY_CDN_HOSTNAME is not configured');
    }

    /**
     * Create a new video in Bunny Stream and trigger fetch from R2.
     *
     * @return array{guid: string, title: string}|null
     */
    public function createVideoFromUrl(string $title, string $sourceUrl): ?array
    {
        $response = $this->client()->post("{$this->libraryId}/videos", [
            'title' => $title,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $video = $response->json();
        $videoId = $video['guid'];

        // Trigger fetch from the source URL
        $fetchResponse = $this->client()->post("{$this->libraryId}/videos/{$videoId}/fetch", [
            'url' => $sourceUrl,
        ]);

        if (! $fetchResponse->successful()) {
            // Clean up the video if fetch fails
            $this->deleteVideo($videoId);

            return null;
        }

        return [
            'guid' => $videoId,
            'title' => $video['title'],
        ];
    }

    /**
     * Get video details from Bunny Stream.
     *
     * @return array{
     *     guid: string,
     *     title: string,
     *     status: int,
     *     length: int,
     *     thumbnailFileName: string|null
     * }|null
     */
    public function getVideo(string $videoId): ?array
    {
        $response = $this->client()->get("{$this->libraryId}/videos/{$videoId}");

        if (! $response->successful()) {
            return null;
        }

        return $response->json();
    }

    /**
     * Delete a video from Bunny Stream.
     */
    public function deleteVideo(string $videoId): bool
    {
        $response = $this->client()->delete("{$this->libraryId}/videos/{$videoId}");

        return $response->successful();
    }

    /**
     * Get the embed URL for a video.
     */
    public function getEmbedUrl(string $videoId): string
    {
        return "https://iframe.mediadelivery.net/embed/{$this->libraryId}/{$videoId}";
    }

    /**
     * Get the direct play URL for a video.
     */
    public function getPlayUrl(string $videoId): string
    {
        return "https://{$this->cdnHostname}/{$videoId}/playlist.m3u8";
    }

    /**
     * Get the thumbnail URL for a video.
     */
    public function getThumbnailUrl(string $videoId): string
    {
        return "https://{$this->cdnHostname}/{$videoId}/thumbnail.jpg";
    }

    /**
     * Map Bunny Stream status code to VideoStatus enum.
     *
     * Bunny statuses:
     * 0 = Created, 1 = Uploaded, 2 = Processing, 3 = Transcoding,
     * 4 = Finished, 5 = Error, 6 = UploadFailed
     */
    public function mapStatus(int $bunnyStatus): VideoStatus
    {
        return match ($bunnyStatus) {
            0, 1 => VideoStatus::Pending,
            2, 3 => VideoStatus::Processing,
            4 => VideoStatus::Ready,
            5, 6 => VideoStatus::Failed,
            default => VideoStatus::Pending,
        };
    }

    /**
     * Get the library ID.
     */
    public function getLibraryId(): string
    {
        return $this->libraryId;
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'AccessKey' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]);
    }
}
