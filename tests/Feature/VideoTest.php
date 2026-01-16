<?php

use App\Enums\VideoStatus;
use App\Models\User;
use App\Models\Video;
use App\Services\BunnyStreamService;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

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
    it('updates video status from webhook', function () {
        $video = Video::factory()->processing()->create([
            'bunny_video_id' => 'webhook-test-video',
        ]);

        $response = postJson('/webhooks/bunny', [
            'VideoId' => 'webhook-test-video',
            'Status' => 4, // Finished
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $video->refresh();
        expect($video->status)->toBe(VideoStatus::Ready);
    });

    it('returns 400 for missing VideoId', function () {
        $response = postJson('/webhooks/bunny', [
            'Status' => 4,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Missing VideoId']);
    });

    it('returns 404 for unknown video', function () {
        $response = postJson('/webhooks/bunny', [
            'VideoId' => 'non-existent-video',
            'Status' => 4,
        ]);

        $response->assertNotFound();
        $response->assertJson(['error' => 'Video not found']);
    });

    it('maps bunny status codes correctly', function () {
        $service = new BunnyStreamService;

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
        $user = User::factory()->create();
        $video = Video::factory()->create([
            'bunny_video_id' => null,
        ]);

        // Mock R2StorageService since bunny_video_id is null, only R2 delete will be called
        $r2Mock = Mockery::mock(\App\Services\R2StorageService::class);
        $r2Mock->shouldReceive('delete')->with($video->r2_path)->once()->andReturn(true);
        app()->instance(\App\Services\R2StorageService::class, $r2Mock);

        actingAs($user);

        Livewire::test('admin.video-manager')
            ->call('confirmDelete', $video->id)
            ->call('deleteVideo');

        expect(Video::find($video->id))->toBeNull();
    });

    it('can delete a video with external resources', function () {
        $user = User::factory()->create();
        $video = Video::factory()->create([
            'bunny_video_id' => 'test-video-id',
            'r2_path' => 'videos/test.mp4',
        ]);

        $bunnyMock = Mockery::mock(\App\Services\BunnyStreamService::class);
        $bunnyMock->shouldReceive('deleteVideo')->with('test-video-id')->once()->andReturn(true);
        app()->instance(\App\Services\BunnyStreamService::class, $bunnyMock);

        $r2Mock = Mockery::mock(\App\Services\R2StorageService::class);
        $r2Mock->shouldReceive('delete')->with('videos/test.mp4')->once()->andReturn(true);
        app()->instance(\App\Services\R2StorageService::class, $r2Mock);

        actingAs($user);

        Livewire::test('admin.video-manager')
            ->call('confirmDelete', $video->id)
            ->call('deleteVideo');

        expect(Video::find($video->id))->toBeNull();
    });
});

describe('Navigation', function () {
    it('shows video link in navigation when published videos exist', function () {
        Video::factory()->create(['is_published' => true, 'status' => VideoStatus::Ready]);

        $response = get('/');

        $response->assertOk();
        $response->assertSee('Video Tutorials');
    });

    it('hides video link when no published videos exist', function () {
        $response = get('/');

        $response->assertOk();
        $response->assertDontSee('Video Tutorials');
    });
});
