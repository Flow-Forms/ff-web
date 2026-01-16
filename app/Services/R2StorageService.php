<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class R2StorageService
{
    private Filesystem $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('r2');
    }

    /**
     * Generate a presigned URL for uploading a file directly to R2.
     *
     * @return array{upload_url: string, path: string, public_url: string}
     */
    public function generatePresignedUploadUrl(string $filename, string $contentType = 'video/mp4', int $expiresInMinutes = 60): array
    {
        $path = $this->generatePath($filename);

        $uploadUrl = $this->disk->temporaryUploadUrl(
            $path,
            now()->addMinutes($expiresInMinutes),
            ['ContentType' => $contentType]
        );

        return [
            'upload_url' => $uploadUrl['url'],
            'path' => $path,
            'public_url' => $this->getPublicUrl($path),
        ];
    }

    /**
     * Generate a presigned URL for downloading/viewing a file from R2.
     */
    public function generatePresignedDownloadUrl(string $path, int $expiresInMinutes = 60): string
    {
        return $this->disk->temporaryUrl($path, now()->addMinutes($expiresInMinutes));
    }

    /**
     * Get the public URL for a file (if bucket has public access).
     */
    public function getPublicUrl(string $path): string
    {
        return $this->disk->url($path);
    }

    /**
     * Check if a file exists in the bucket.
     */
    public function exists(string $path): bool
    {
        return $this->disk->exists($path);
    }

    /**
     * Delete a file from the bucket.
     */
    public function delete(string $path): bool
    {
        return $this->disk->delete($path);
    }

    /**
     * Generate a unique path for a video file.
     */
    private function generatePath(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION) ?: 'mp4';
        $date = now()->format('Y/m');

        return "videos/{$date}/".uniqid().'.'.$extension;
    }
}
