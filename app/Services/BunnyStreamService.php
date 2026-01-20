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
     * Set a custom thumbnail for a video from an external URL.
     */
    public function setThumbnail(string $videoId, string $thumbnailUrl): bool
    {
        $response = $this->client()->post(
            "{$this->libraryId}/videos/{$videoId}/thumbnail?thumbnailUrl=".urlencode($thumbnailUrl)
        );

        return $response->successful();
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

    /**
     * Trigger transcription for a video with AI-generated metadata.
     *
     * @param  array{
     *     language?: string,
     *     generateTitle?: bool,
     *     generateDescription?: bool,
     *     generateChapters?: bool,
     *     generateMoments?: bool
     * }  $options
     */
    public function transcribeVideo(string $videoId, array $options = []): bool
    {
        $defaults = [
            'language' => 'en',
            'generateTitle' => true,
            'generateDescription' => true,
            'generateChapters' => true,
            'generateMoments' => true,
        ];

        $payload = array_merge($defaults, $options);

        $response = $this->client()->post("{$this->libraryId}/videos/{$videoId}/transcribe", $payload);

        return $response->successful();
    }

    /**
     * Get the caption/subtitle content for a video.
     * Returns the VTT file content as a string.
     */
    public function getCaptionContent(string $videoId, string $language = 'en'): ?string
    {
        $url = "https://{$this->cdnHostname}/{$videoId}/captions/{$language}.vtt";

        $response = Http::get($url);

        if (! $response->successful()) {
            return null;
        }

        return $response->body();
    }

    /**
     * Parse VTT caption content into plain text transcript.
     * Removes timestamps and formatting, returns just the spoken text.
     */
    public function parseVttToTranscript(string $vttContent): string
    {
        $lines = explode("\n", $vttContent);
        $transcript = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip WEBVTT header
            if ($line === 'WEBVTT' || str_starts_with($line, 'NOTE')) {
                continue;
            }

            // Skip empty lines
            if (empty($line)) {
                continue;
            }

            // Skip timestamp lines (contain -->)
            if (str_contains($line, '-->')) {
                continue;
            }

            // Skip cue identifiers (numeric or alphanumeric identifiers before timestamps)
            if (preg_match('/^[\d\w-]+$/', $line) && strlen($line) < 20) {
                continue;
            }

            // This is actual caption text - strip any VTT formatting tags
            $text = preg_replace('/<[^>]+>/', '', $line);
            $text = trim($text);

            if (! empty($text)) {
                $transcript[] = $text;
            }
        }

        return implode(' ', $transcript);
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
