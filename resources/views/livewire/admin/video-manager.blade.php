<?php

use App\Enums\VideoStatus;
use App\Models\Video;
use App\Services\BunnyStreamService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $title = '';
    public string $description = '';
    public bool $showUploadModal = false;
    public ?int $deletingVideoId = null;
    public ?int $editingVideoId = null;
    public string $editTitle = '';
    public string $editDescription = '';

    #[Computed]
    public function videos()
    {
        return Video::query()->ordered()->get();
    }

    public function openUploadModal(): void
    {
        $this->reset(['title', 'description']);
        $this->showUploadModal = true;
    }

    public function closeUploadModal(): void
    {
        $this->showUploadModal = false;
        $this->reset(['title', 'description']);
    }

    public function getPresignedUrl(string $filename, string $contentType): array
    {
        $path = $this->generateVideoPath($filename);

        $uploadUrl = Storage::disk('s3')->temporaryUploadUrl(
            $path,
            now()->addHour(),
            ['ContentType' => $contentType]
        );

        return [
            'upload_url' => $uploadUrl['url'],
            'path' => $path,
            'public_url' => Storage::disk('s3')->url($path),
        ];
    }

    public function createVideo(string $title, string $description, string $r2Path): array
    {
        $bunny = app(BunnyStreamService::class);

        $video = Video::create([
            'title' => $title,
            'slug' => Video::generateUniqueSlug($title),
            'description' => $description ?: null,
            'r2_path' => $r2Path,
            'bunny_library_id' => $bunny->getLibraryId(),
            'status' => VideoStatus::Pending,
        ]);

        $sourceUrl = Storage::disk('s3')->url($r2Path);
        $result = $bunny->createVideoFromUrl($title, $sourceUrl);

        if ($result) {
            $video->update([
                'bunny_video_id' => $result['guid'],
                'status' => VideoStatus::Processing,
            ]);
        } else {
            $video->update(['status' => VideoStatus::Failed]);
        }

        unset($this->videos);

        return ['success' => (bool) $result, 'video' => $video->toArray()];
    }

    public function togglePublish(int $videoId): void
    {
        $video = Video::findOrFail($videoId);
        $video->update([
            'is_published' => !$video->is_published,
            'published_at' => !$video->is_published ? now() : null,
        ]);
        unset($this->videos);
    }

    public function confirmDelete(int $videoId): void
    {
        $this->deletingVideoId = $videoId;
        $this->modal('delete-confirm')->show();
    }

    public function cancelDelete(): void
    {
        $this->modal('delete-confirm')->close();
        $this->deletingVideoId = null;
    }

    public function deleteVideo(): void
    {
        if ($this->deletingVideoId) {
            $video = Video::findOrFail($this->deletingVideoId);

            if ($video->bunny_video_id) {
                app(BunnyStreamService::class)->deleteVideo($video->bunny_video_id);
            }

            if ($video->r2_path) {
                Storage::disk('s3')->delete($video->r2_path);
            }

            $video->delete();
            $this->modal('delete-confirm')->close();
            $this->deletingVideoId = null;
            unset($this->videos);
        }
    }

    public function refreshVideos(): void
    {
        unset($this->videos);
    }

    public function syncStatus(int $videoId): void
    {
        $video = Video::findOrFail($videoId);

        if (! $video->bunny_video_id) {
            return;
        }

        $bunny = app(BunnyStreamService::class);
        $bunnyVideo = $bunny->getVideo($video->bunny_video_id);

        if (! $bunnyVideo) {
            return;
        }

        $newStatus = $bunny->mapStatus($bunnyVideo['status']);

        $updateData = ['status' => $newStatus];

        if ($newStatus === VideoStatus::Ready) {
            $updateData['duration_seconds'] = $bunnyVideo['length'] ?? null;
            $updateData['thumbnail_url'] = $bunny->getThumbnailUrl($video->bunny_video_id);
        }

        $video->update($updateData);
        unset($this->videos);
    }

    public function openEditModal(int $videoId): void
    {
        $video = Video::findOrFail($videoId);
        $this->editingVideoId = $videoId;
        $this->editTitle = $video->title;
        $this->editDescription = $video->description ?? '';
        $this->modal('edit-video')->show();
    }

    public function cancelEdit(): void
    {
        $this->modal('edit-video')->close();
        $this->reset(['editingVideoId', 'editTitle', 'editDescription']);
    }

    public function saveEdit(): void
    {
        if ($this->editingVideoId) {
            $video = Video::findOrFail($this->editingVideoId);
            $video->update([
                'title' => $this->editTitle,
                'description' => $this->editDescription ?: null,
            ]);
            $this->modal('edit-video')->close();
            $this->reset(['editingVideoId', 'editTitle', 'editDescription']);
            unset($this->videos);
        }
    }

    private function generateVideoPath(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION) ?: 'mp4';
        $date = now()->format('Y/m');

        return "videos/{$date}/".uniqid().'.'.$extension;
    }
};
?>

<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">Video Manager</flux:heading>
        <flux:button variant="primary" icon="plus" wire:click="openUploadModal">
            Upload Video
        </flux:button>
    </div>

    {{-- Video List --}}
    <div class="space-y-4" wire:poll.5s="refreshVideos">
        @forelse($this->videos as $video)
            <flux:card wire:key="video-{{ $video->id }}" class="!p-4">
                <div class="flex items-start gap-4">
                    {{-- Thumbnail --}}
                    <div style="width: 160px; height: 96px; flex-shrink: 0;" class="bg-zinc-100 dark:bg-zinc-800 rounded-lg overflow-hidden">
                        @if($video->thumbnail_url)
                            <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" style="width: 160px; height: 96px; object-fit: cover;">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <flux:icon.film class="size-8 text-zinc-400" />
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <flux:heading size="lg" class="truncate">{{ $video->title }}</flux:heading>
                                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                                    /video/{{ $video->slug }}
                                    @if($video->duration_seconds)
                                        <span class="mx-2">&bull;</span>
                                        {{ $video->getFormattedDuration() }}
                                    @endif
                                </flux:text>
                            </div>

                            {{-- Status Badge --}}
                            <flux:badge :color="$video->status->color()">{{ $video->status->label() }}</flux:badge>
                        </div>

                        @if($video->description)
                            <flux:text class="mt-2 line-clamp-2">{{ strip_tags($video->description) }}</flux:text>
                        @endif

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 mt-4">
                            @if($video->isReady())
                                <flux:button
                                    size="sm"
                                    :variant="$video->is_published ? 'filled' : 'outline'"
                                    wire:click="togglePublish({{ $video->id }})"
                                >
                                    {{ $video->is_published ? 'Unpublish' : 'Publish' }}
                                </flux:button>

                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    href="/video/{{ $video->slug }}"
                                    target="_blank"
                                    icon="eye"
                                >
                                    Preview
                                </flux:button>
                            @else
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    icon="arrow-path"
                                    wire:click="syncStatus({{ $video->id }})"
                                >
                                    Sync Status
                                </flux:button>
                            @endif

                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="pencil"
                                wire:click="openEditModal({{ $video->id }})"
                            >
                                Edit
                            </flux:button>

                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="trash"
                                wire:click="confirmDelete({{ $video->id }})"
                            >
                                Delete
                            </flux:button>
                        </div>
                    </div>
                </div>
            </flux:card>
        @empty
            <flux:card>
                <div class="p-8 text-center">
                    <flux:icon.film class="size-12 mx-auto text-zinc-400 mb-4" />
                    <flux:heading size="lg" class="mb-2">No videos yet</flux:heading>
                    <flux:text class="mb-4">Upload your first video to get started.</flux:text>
                    <flux:button variant="primary" wire:click="openUploadModal">
                        Upload Video
                    </flux:button>
                </div>
            </flux:card>
        @endforelse
    </div>

    {{-- Upload Modal --}}
    <flux:modal name="upload-modal" :show="$showUploadModal" wire:model="showUploadModal" class="max-w-lg">
        <div class="p-6">
            <flux:heading size="lg" class="mb-4">Upload Video</flux:heading>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Title</flux:label>
                    <flux:input wire:model="title" placeholder="Video title" />
                </flux:field>

                <flux:field>
                    <flux:label>Description (optional)</flux:label>
                    <flux:textarea wire:model="description" placeholder="Brief description of the video" rows="3" />
                </flux:field>

                <flux:field>
                    <flux:label>Video File</flux:label>
                    <div
                        x-data="videoUploader()"
                        class="space-y-4"
                    >
                        <div
                            x-on:dragover.prevent="dragover = true"
                            x-on:dragleave.prevent="dragover = false"
                            x-on:drop.prevent="handleDrop($event)"
                            :class="{ 'border-blue-500 bg-blue-50 dark:bg-blue-900/20': dragover }"
                            class="border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-8 text-center transition-colors"
                        >
                            <template x-if="!file && !uploading">
                                <div>
                                    <flux:icon.cloud-arrow-up class="size-10 mx-auto text-zinc-400 mb-3" />
                                    <flux:text class="mb-2">Drag and drop your video here, or</flux:text>
                                    <flux:button size="sm" x-on:click="$refs.fileInput.click()">
                                        Browse Files
                                    </flux:button>
                                    <input
                                        type="file"
                                        x-ref="fileInput"
                                        x-on:change="handleFileSelect($event)"
                                        accept="video/mp4,video/quicktime,video/webm"
                                        class="hidden"
                                    >
                                    <flux:text class="text-xs text-zinc-500 mt-2">MP4, MOV, or WebM up to 2GB</flux:text>
                                </div>
                            </template>

                            <template x-if="file && !uploading">
                                <div>
                                    <flux:icon.film class="size-10 mx-auto text-zinc-400 mb-3" />
                                    <flux:text class="font-medium" x-text="file.name"></flux:text>
                                    <flux:text class="text-sm text-zinc-500" x-text="formatSize(file.size)"></flux:text>
                                    <div class="mt-3">
                                        <flux:button size="sm" variant="ghost" x-on:click="clearFile()">
                                            Remove
                                        </flux:button>
                                    </div>
                                </div>
                            </template>

                            <template x-if="uploading">
                                <div>
                                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 mb-3">
                                        <div
                                            class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                            :style="{ width: progress + '%' }"
                                        ></div>
                                    </div>
                                    <flux:text class="text-sm">
                                        <span x-text="status"></span>
                                        <span x-show="progress > 0 && progress < 100" x-text="'(' + progress + '%)'"></span>
                                    </flux:text>
                                </div>
                            </template>
                        </div>

                        <template x-if="error">
                            <flux:callout variant="danger" x-text="error" />
                        </template>

                        <div class="flex justify-end gap-3">
                            <flux:button variant="ghost" wire:click="closeUploadModal" x-bind:disabled="uploading">
                                Cancel
                            </flux:button>
                            <flux:button
                                variant="primary"
                                x-on:click="startUpload()"
                                x-bind:disabled="!file || !$wire.title || uploading"
                            >
                                <span x-show="!uploading">Upload</span>
                                <span x-show="uploading">Uploading...</span>
                            </flux:button>
                        </div>
                    </div>
                </flux:field>
            </div>
        </div>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="delete-confirm" class="max-w-sm">
        <div class="p-6 text-center">
            <flux:icon.exclamation-triangle class="size-12 mx-auto text-red-500 mb-4" />
            <flux:heading size="lg" class="mb-2">Delete Video?</flux:heading>
            <flux:text class="mb-6">This action cannot be undone. The video will be permanently removed.</flux:text>
            <div class="flex justify-center gap-3">
                <flux:button variant="ghost" wire:click="cancelDelete">Cancel</flux:button>
                <flux:button variant="danger" wire:click="deleteVideo">Delete</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Edit Video Modal --}}
    <flux:modal name="edit-video" class="max-w-2xl">
        <div class="p-6">
            <flux:heading size="lg" class="mb-6">Edit Video</flux:heading>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Title</flux:label>
                    <flux:input wire:model="editTitle" placeholder="Video title" />
                </flux:field>

                <flux:field>
                    <flux:label>Description</flux:label>
                    <flux:editor wire:model="editDescription" placeholder="Describe this video..." />
                </flux:field>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button variant="ghost" wire:click="cancelEdit">Cancel</flux:button>
                    <flux:button variant="primary" wire:click="saveEdit">Save Changes</flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>

@script
<script>
    Alpine.data('videoUploader', () => ({
        file: null,
        uploading: false,
        progress: 0,
        status: '',
        error: null,
        dragover: false,

        handleFileSelect(event) {
            this.setFile(event.target.files[0]);
        },

        handleDrop(event) {
            this.dragover = false;
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('video/')) {
                this.setFile(file);
            }
        },

        setFile(file) {
            if (file && file.size > 2 * 1024 * 1024 * 1024) {
                this.error = 'File size must be less than 2GB';
                return;
            }
            this.file = file;
            this.error = null;
        },

        clearFile() {
            this.file = null;
            this.error = null;
        },

        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            if (bytes < 1024 * 1024 * 1024) return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            return (bytes / (1024 * 1024 * 1024)).toFixed(2) + ' GB';
        },

        async startUpload() {
            if (!this.file || !$wire.title) return;

            this.uploading = true;
            this.progress = 0;
            this.error = null;
            this.status = 'Getting upload URL...';

            try {
                // Get presigned URL from Livewire
                const { upload_url, path } = await $wire.getPresignedUrl(
                    this.file.name,
                    this.file.type || 'video/mp4'
                );

                // Upload to R2
                this.status = 'Uploading to storage...';
                await this.uploadToR2(upload_url, this.file);

                // Create video record
                this.status = 'Processing...';
                this.progress = 100;

                const result = await $wire.createVideo($wire.title, $wire.description, path);

                if (result.success) {
                    this.status = 'Complete!';
                    setTimeout(() => {
                        this.reset();
                        $wire.closeUploadModal();
                    }, 1000);
                } else {
                    throw new Error('Failed to create video');
                }

            } catch (err) {
                this.error = err.message || 'Upload failed';
                this.uploading = false;
            }
        },

        async uploadToR2(url, file) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();

                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        this.progress = Math.round((e.loaded / e.total) * 95);
                    }
                });

                xhr.addEventListener('load', () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve();
                    } else {
                        reject(new Error('Upload failed'));
                    }
                });

                xhr.addEventListener('error', () => reject(new Error('Upload failed')));

                xhr.open('PUT', url);
                xhr.setRequestHeader('Content-Type', file.type || 'video/mp4');
                xhr.send(file);
            });
        },

        reset() {
            this.file = null;
            this.uploading = false;
            this.progress = 0;
            this.status = '';
            this.error = null;
        },
    }));
</script>
@endscript
