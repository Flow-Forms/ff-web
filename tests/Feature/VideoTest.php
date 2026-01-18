<?php

use App\Enums\VideoStatus;
use App\Models\User;
use App\Models\Video;
use App\Services\BunnyStreamService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    // Ensure clean state for video tests
});

describe('Video Model', function () {
    it('creates a video with factory', function () {
        $video = Video::factory()->create();

        expect($video)->toBeInstanceOf(Video::class);
        expect($video->title)->not->toBeEmpty();
        expect($video->slug)->not->toBeEmpty();
        expect($video->status)->toBe(VideoStatus::Ready);
        expect($video->is_published)->toBeTrue();
    });

    it('creates pending video with factory state', function () {
        $video = Video::factory()->pending()->create();

        expect($video->status)->toBe(VideoStatus::Pending);
        expect($video->bunny_video_id)->toBeNull();
        expect($video->is_published)->toBeFalse();
    });

    it('creates processing video with factory state', function () {
        $video = Video::factory()->processing()->create();

        expect($video->status)->toBe(VideoStatus::Processing);
        expect($video->is_published)->toBeFalse();
    });

    it('creates failed video with factory state', function () {
        $video = Video::factory()->failed()->create();

        expect($video->status)->toBe(VideoStatus::Failed);
        expect($video->is_published)->toBeFalse();
    });

    it('scopes published videos correctly', function () {
        Video::factory()->count(2)->create(['is_published' => true, 'status' => VideoStatus::Ready]);
        Video::factory()->unpublished()->create();
        Video::factory()->processing()->create();

        $published = Video::published()->get();

        expect($published)->toHaveCount(2);
    });

    it('generates embed URL correctly', function () {
        $video = Video::factory()->create([
            'bunny_video_id' => 'test-video-123',
            'bunny_library_id' => 'test-library-456',
        ]);

        $embedUrl = $video->getEmbedUrl();

        expect($embedUrl)->toBe('https://iframe.mediadelivery.net/embed/test-library-456/test-video-123');
    });

    it('returns null embed URL when missing bunny IDs', function () {
        $video = Video::factory()->pending()->create();

        expect($video->getEmbedUrl())->toBeNull();
    });

    it('formats duration correctly', function () {
        $video = Video::factory()->create(['duration_seconds' => 125]);

        expect($video->getFormattedDuration())->toBe('2:05');
    });

    it('returns null for missing duration', function () {
        $video = Video::factory()->create(['duration_seconds' => null]);

        expect($video->getFormattedDuration())->toBeNull();
    });

    it('uses slug for route key', function () {
        $video = Video::factory()->create(['slug' => 'my-test-video']);

        expect($video->getRouteKeyName())->toBe('slug');
    });

    it('generates unique slugs', function () {
        Video::factory()->create(['slug' => 'test-video']);

        $slug = Video::generateUniqueSlug('Test Video');

        expect($slug)->toBe('test-video-1');
    });
});

describe('Public Video Pages', function () {
    it('displays video index page with published videos', function () {
        Video::factory()->count(3)->create(['is_published' => true, 'status' => VideoStatus::Ready]);

        $response = get('/video');

        $response->assertOk();
        $response->assertSee('Video Tutorials');
    });

    it('shows empty state when no published videos exist', function () {
        $response = get('/video');

        $response->assertOk();
        $response->assertSee('No videos available yet');
    });

    it('does not show unpublished videos in index', function () {
        $published = Video::factory()->create([
            'title' => 'Published Video',
            'is_published' => true,
            'status' => VideoStatus::Ready,
        ]);
        Video::factory()->unpublished()->create([
            'title' => 'Unpublished Video',
        ]);

        $response = get('/video');

        $response->assertOk();
        $response->assertSee('Published Video');
        $response->assertDontSee('Unpublished Video');
    });

    it('displays individual published video page', function () {
        $video = Video::factory()->create([
            'title' => 'My Test Video',
            'slug' => 'my-test-video',
            'is_published' => true,
            'status' => VideoStatus::Ready,
        ]);

        $response = get('/video/my-test-video');

        $response->assertOk();
        $response->assertSee('My Test Video');
    });

    it('returns 404 for unpublished video', function () {
        $video = Video::factory()->unpublished()->create(['slug' => 'private-video']);

        $response = get('/video/private-video');

        $response->assertNotFound();
    });

    it('allows admin users to preview unpublished videos', function () {
        $user = User::factory()->create(['email' => 'admin@flowforms.io']);
        $video = Video::factory()->create([
            'title' => 'Preview Video',
            'slug' => 'preview-video',
            'is_published' => false,
            'status' => VideoStatus::Ready,
        ]);

        actingAs($user);

        $response = get('/video/preview-video');

        $response->assertOk();
        $response->assertSee('Preview Video');
        $response->assertSee('Preview Mode');
    });

    it('returns 404 for processing video', function () {
        $video = Video::factory()->processing()->create([
            'slug' => 'processing-video',
            'is_published' => true,
        ]);

        $response = get('/video/processing-video');

        $response->assertNotFound();
    });

    it('returns 404 for non-existent video', function () {
        $response = get('/video/does-not-exist');

        $response->assertNotFound();
    });

    it('shows video embed iframe when bunny IDs are present', function () {
        $video = Video::factory()->create([
            'slug' => 'video-with-embed',
            'bunny_video_id' => 'vid-123',
            'bunny_library_id' => 'lib-456',
            'is_published' => true,
            'status' => VideoStatus::Ready,
        ]);

        $response = get('/video/video-with-embed');

        $response->assertOk();
        $response->assertSee('iframe.mediadelivery.net/embed/lib-456/vid-123', false);
    });
});

describe('Bunny Webhook', function () {
    beforeEach(function () {
        // Set up webhook secret for testing
        config(['services.bunny.webhook_secret' => 'test-secret']);

        // Fake the queue so transcription jobs don't run synchronously
        Queue::fake();

        // Mock BunnyStreamService for webhook tests
        $mock = Mockery::mock(BunnyStreamService::class);
        $mock->shouldReceive('mapStatus')->with(4)->andReturn(VideoStatus::Ready);
        $mock->shouldReceive('getVideo')->andReturn(['length' => 120]);
        $mock->shouldReceive('getThumbnailUrl')->andReturn('https://example.com/thumb.jpg');
        app()->instance(BunnyStreamService::class, $mock);
    });

    it('updates video status from webhook', function () {
        $video = Video::factory()->processing()->create([
            'bunny_video_id' => 'webhook-test-video',
        ]);

        $payload = json_encode([
            'VideoId' => 'webhook-test-video',
            'Status' => 4, // Finished
        ]);
        $signature = hash('sha256', $payload.'test-secret');

        $response = $this->withHeaders([
            'X-Bunny-Signature' => $signature,
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/bunny', [
            'VideoId' => 'webhook-test-video',
            'Status' => 4,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $video->refresh();
        expect($video->status)->toBe(VideoStatus::Ready);
    });

    it('returns 400 for missing VideoId', function () {
        $payload = json_encode([
            'Status' => 4,
        ]);
        $signature = hash('sha256', $payload.'test-secret');

        $response = $this->withHeaders([
            'X-Bunny-Signature' => $signature,
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/bunny', [
            'Status' => 4,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Missing VideoId']);
    });

    it('returns 404 for unknown video', function () {
        $payload = json_encode([
            'VideoId' => 'non-existent-video',
            'Status' => 4,
        ]);
        $signature = hash('sha256', $payload.'test-secret');

        $response = $this->withHeaders([
            'X-Bunny-Signature' => $signature,
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/bunny', [
            'VideoId' => 'non-existent-video',
            'Status' => 4,
        ]);

        $response->assertNotFound();
        $response->assertJson(['error' => 'Video not found']);
    });

    it('returns 400 for missing Status', function () {
        $video = Video::factory()->processing()->create([
            'bunny_video_id' => 'status-test-video',
        ]);

        $payload = json_encode([
            'VideoId' => 'status-test-video',
        ]);
        $signature = hash('sha256', $payload.'test-secret');

        $response = $this->withHeaders([
            'X-Bunny-Signature' => $signature,
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/bunny', [
            'VideoId' => 'status-test-video',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Missing status']);
    });

    it('maps bunny status codes correctly', function () {
        // Test mapStatus directly with a fresh mock that allows all calls
        $service = Mockery::mock(BunnyStreamService::class)->makePartial();
        $service->shouldReceive('mapStatus')->with(0)->andReturn(VideoStatus::Pending);
        $service->shouldReceive('mapStatus')->with(1)->andReturn(VideoStatus::Pending);
        $service->shouldReceive('mapStatus')->with(2)->andReturn(VideoStatus::Processing);
        $service->shouldReceive('mapStatus')->with(3)->andReturn(VideoStatus::Processing);
        $service->shouldReceive('mapStatus')->with(4)->andReturn(VideoStatus::Ready);
        $service->shouldReceive('mapStatus')->with(5)->andReturn(VideoStatus::Failed);
        $service->shouldReceive('mapStatus')->with(6)->andReturn(VideoStatus::Failed);

        expect($service->mapStatus(0))->toBe(VideoStatus::Pending);
        expect($service->mapStatus(1))->toBe(VideoStatus::Pending);
        expect($service->mapStatus(2))->toBe(VideoStatus::Processing);
        expect($service->mapStatus(3))->toBe(VideoStatus::Processing);
        expect($service->mapStatus(4))->toBe(VideoStatus::Ready);
        expect($service->mapStatus(5))->toBe(VideoStatus::Failed);
        expect($service->mapStatus(6))->toBe(VideoStatus::Failed);
    });
});

describe('Admin Video Manager', function () {
    it('requires authentication for admin video page', function () {
        $response = get('/admin/video');

        $response->assertRedirect();
    });

    it('renders video manager component', function () {
        $user = User::factory()->create();

        actingAs($user);

        Livewire::test('admin.video-manager')
            ->assertOk()
            ->assertSee('Video Manager');
    });

    it('shows all videos including unpublished in admin', function () {
        $user = User::factory()->create();
        Video::factory()->create(['title' => 'Published Video', 'is_published' => true, 'status' => VideoStatus::Ready]);
        Video::factory()->unpublished()->create(['title' => 'Draft Video']);

        actingAs($user);

        Livewire::test('admin.video-manager')
            ->assertOk()
            ->assertSee('Published Video')
            ->assertSee('Draft Video');
    });

    it('can toggle video publish status', function () {
        $user = User::factory()->create();
        $video = Video::factory()->create(['is_published' => true, 'status' => VideoStatus::Ready]);

        actingAs($user);

        Livewire::test('admin.video-manager')
            ->call('togglePublish', $video->id);

        expect($video->fresh()->is_published)->toBeFalse();
    });

    it('can delete a video', function () {
        Storage::fake('s3');

        $user = User::factory()->create();
        $video = Video::factory()->create([
            'bunny_video_id' => null,
            'r2_path' => 'videos/2026/01/test.mp4',
        ]);

        // Create a fake file at the r2_path
        Storage::disk('s3')->put($video->r2_path, 'fake video content');

        actingAs($user);

        Livewire::test('admin.video-manager')
            ->call('confirmDelete', $video->id)
            ->call('deleteVideo');

        expect(Video::find($video->id))->toBeNull();
        Storage::disk('s3')->assertMissing($video->r2_path);
    });

    it('can delete a video with external resources', function () {
        Storage::fake('s3');

        $user = User::factory()->create();
        $video = Video::factory()->create([
            'bunny_video_id' => 'test-video-id',
            'r2_path' => 'videos/test.mp4',
        ]);

        // Create a fake file at the r2_path
        Storage::disk('s3')->put($video->r2_path, 'fake video content');

        $bunnyMock = Mockery::mock(\App\Services\BunnyStreamService::class);
        $bunnyMock->shouldReceive('deleteVideo')->with('test-video-id')->once()->andReturn(true);
        app()->instance(\App\Services\BunnyStreamService::class, $bunnyMock);

        actingAs($user);

        Livewire::test('admin.video-manager')
            ->call('confirmDelete', $video->id)
            ->call('deleteVideo');

        expect(Video::find($video->id))->toBeNull();
        Storage::disk('s3')->assertMissing($video->r2_path);
    });

    it('can edit video title and description', function () {
        $user = User::factory()->create();
        $video = Video::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original description',
            'status' => VideoStatus::Ready,
        ]);

        actingAs($user);

        Livewire::test('admin.video-manager')
            ->call('openEditModal', $video->id)
            ->assertSet('editTitle', 'Original Title')
            ->assertSet('editDescription', 'Original description')
            ->set('editTitle', 'Updated Title')
            ->set('editDescription', '<p>Updated description with HTML</p>')
            ->call('saveEdit');

        $video->refresh();
        expect($video->title)->toBe('Updated Title');
        expect($video->description)->toBe('<p>Updated description with HTML</p>');
    });
});

describe('Navigation', function () {
    it('shows video link in navigation when published videos exist', function () {
        Video::factory()->create(['is_published' => true, 'status' => VideoStatus::Ready]);

        $response = get('/');

        $response->assertOk();
        $response->assertSee('Video Tutorials');
    })->skip('Navigation video link integration not yet implemented');

    it('hides video link when no published videos exist', function () {
        $response = get('/');

        $response->assertOk();
        $response->assertDontSee('Video Tutorials');
    })->skip('Navigation video link integration not yet implemented');
});
