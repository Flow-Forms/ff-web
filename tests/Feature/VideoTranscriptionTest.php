<?php

use App\Enums\TranscriptionStatus;
use App\Enums\VideoStatus;
use App\Jobs\SyncVideoTranscription;
use App\Jobs\TriggerVideoTranscription;
use App\Models\User;
use App\Models\Video;
use App\Services\BunnyStreamService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('Transcription Status Enum', function () {
    it('has correct labels', function () {
        expect(TranscriptionStatus::Pending->label())->toBe('Pending');
        expect(TranscriptionStatus::Processing->label())->toBe('Transcribing');
        expect(TranscriptionStatus::Completed->label())->toBe('Transcribed');
        expect(TranscriptionStatus::Failed->label())->toBe('Failed');
    });

    it('has correct colors', function () {
        expect(TranscriptionStatus::Pending->color())->toBe('zinc');
        expect(TranscriptionStatus::Processing->color())->toBe('yellow');
        expect(TranscriptionStatus::Completed->color())->toBe('green');
        expect(TranscriptionStatus::Failed->color())->toBe('red');
    });
});

describe('Video Transcription Model', function () {
    it('creates transcribing video with factory state', function () {
        $video = Video::factory()->transcribing()->create();

        expect($video->status)->toBe(VideoStatus::Ready);
        expect($video->transcription_status)->toBe(TranscriptionStatus::Processing);
        expect($video->isTranscribing())->toBeTrue();
    });

    it('creates transcribed video with factory state', function () {
        $video = Video::factory()->transcribed()->create();

        expect($video->status)->toBe(VideoStatus::Ready);
        expect($video->transcription_status)->toBe(TranscriptionStatus::Completed);
        expect($video->isTranscribed())->toBeTrue();
        expect($video->hasTranscript())->toBeTrue();
        expect($video->hasChapters())->toBeTrue();
        expect($video->transcribed_at)->not->toBeNull();
    });

    it('creates transcription failed video with factory state', function () {
        $video = Video::factory()->transcriptionFailed()->create();

        expect($video->status)->toBe(VideoStatus::Ready);
        expect($video->transcription_status)->toBe(TranscriptionStatus::Failed);
    });

    it('returns correct combined status for processing video', function () {
        $video = Video::factory()->processing()->create();

        expect($video->getCombinedStatusLabel())->toBe('Processing');
        expect($video->getCombinedStatusColor())->toBe('yellow');
    });

    it('returns correct combined status for transcribing video', function () {
        $video = Video::factory()->transcribing()->create();

        expect($video->getCombinedStatusLabel())->toBe('Transcribing');
        expect($video->getCombinedStatusColor())->toBe('yellow');
    });

    it('returns correct combined status for transcribed video', function () {
        $video = Video::factory()->transcribed()->create();

        expect($video->getCombinedStatusLabel())->toBe('Ready');
        expect($video->getCombinedStatusColor())->toBe('green');
    });

    it('returns correct combined status for transcription failed video', function () {
        $video = Video::factory()->transcriptionFailed()->create();

        expect($video->getCombinedStatusLabel())->toBe('Transcription Failed');
        expect($video->getCombinedStatusColor())->toBe('red');
    });

    it('identifies videos needing transcription', function () {
        $ready = Video::factory()->create([
            'status' => VideoStatus::Ready,
            'transcription_status' => TranscriptionStatus::Pending,
        ]);
        $transcribed = Video::factory()->transcribed()->create();
        $processing = Video::factory()->processing()->create();

        expect($ready->needsTranscription())->toBeTrue();
        expect($transcribed->needsTranscription())->toBeFalse();
        expect($processing->needsTranscription())->toBeFalse();
    });
});

describe('TriggerVideoTranscription Job', function () {
    it('triggers transcription via Bunny API', function () {
        Http::fake([
            'video.bunnycdn.com/*' => Http::response(['success' => true], 200),
        ]);

        $video = Video::factory()->create([
            'bunny_video_id' => 'test-video-123',
            'transcription_status' => TranscriptionStatus::Pending,
        ]);

        TriggerVideoTranscription::dispatchSync($video);

        $video->refresh();
        expect($video->transcription_status)->toBe(TranscriptionStatus::Processing);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/transcribe');
        });
    });

    it('sets failed status when API call fails', function () {
        Http::fake([
            'video.bunnycdn.com/*' => Http::response(['error' => 'Failed'], 500),
        ]);

        $video = Video::factory()->create([
            'bunny_video_id' => 'test-video-123',
            'transcription_status' => TranscriptionStatus::Pending,
        ]);

        TriggerVideoTranscription::dispatchSync($video);

        $video->refresh();
        expect($video->transcription_status)->toBe(TranscriptionStatus::Failed);
    });

    it('skips videos without bunny_video_id', function () {
        $video = Video::factory()->pending()->create();

        TriggerVideoTranscription::dispatchSync($video);

        $video->refresh();
        expect($video->transcription_status)->toBe(TranscriptionStatus::Pending);
    });
});

describe('SyncVideoTranscription Job', function () {
    it('syncs transcript data from Bunny', function () {
        $vttContent = "WEBVTT\n\n00:00:00.000 --> 00:00:05.000\nHello world\n\n00:00:05.000 --> 00:00:10.000\nThis is a test";

        Http::fake([
            'video.bunnycdn.com/*' => Http::response([
                'guid' => 'test-video-123',
                'title' => 'AI Generated Title',
                'chapters' => [
                    ['title' => 'Intro', 'start' => 0, 'end' => 60],
                ],
            ], 200),
            '*.b-cdn.net/*' => Http::response($vttContent, 200),
        ]);

        $video = Video::factory()->create([
            'bunny_video_id' => 'test-video-123',
            'title' => 'video-upload.mp4', // Placeholder title
            'transcription_status' => TranscriptionStatus::Processing,
        ]);

        SyncVideoTranscription::dispatchSync($video);

        $video->refresh();
        expect($video->transcription_status)->toBe(TranscriptionStatus::Completed);
        expect($video->transcript)->toContain('Hello world');
        expect($video->chapters)->not->toBeNull();
        expect($video->transcribed_at)->not->toBeNull();
    });
});

describe('Bunny Webhook Transcription Events', function () {
    beforeEach(function () {
        config(['services.bunny.webhook_secret' => 'test-secret']);
        Queue::fake();
    });

    it('dispatches transcription job when video finishes processing', function () {
        $mock = Mockery::mock(BunnyStreamService::class);
        $mock->shouldReceive('mapStatus')->with(4)->andReturn(VideoStatus::Ready);
        $mock->shouldReceive('getVideo')->andReturn(['length' => 120]);
        $mock->shouldReceive('getThumbnailUrl')->andReturn('https://example.com/thumb.jpg');
        app()->instance(BunnyStreamService::class, $mock);

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

        Queue::assertPushed(TriggerVideoTranscription::class, function ($job) use ($video) {
            return $job->video->id === $video->id;
        });
    });

    it('dispatches sync job when captions are generated', function () {
        $video = Video::factory()->transcribing()->create([
            'bunny_video_id' => 'transcription-test-video',
        ]);

        $payload = json_encode([
            'VideoId' => 'transcription-test-video',
            'Status' => 9, // CaptionsGenerated
        ]);
        $signature = hash('sha256', $payload.'test-secret');

        $response = $this->withHeaders([
            'X-Bunny-Signature' => $signature,
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/bunny', [
            'VideoId' => 'transcription-test-video',
            'Status' => 9,
        ]);

        $response->assertOk();
        $response->assertJson(['event' => 'transcription']);

        Queue::assertPushed(SyncVideoTranscription::class, function ($job) use ($video) {
            return $job->video->id === $video->id;
        });
    });

    it('dispatches sync job when title/description are generated', function () {
        $video = Video::factory()->transcribing()->create([
            'bunny_video_id' => 'title-test-video',
        ]);

        $payload = json_encode([
            'VideoId' => 'title-test-video',
            'Status' => 10, // TitleOrDescriptionGenerated
        ]);
        $signature = hash('sha256', $payload.'test-secret');

        $response = $this->withHeaders([
            'X-Bunny-Signature' => $signature,
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/bunny', [
            'VideoId' => 'title-test-video',
            'Status' => 10,
        ]);

        $response->assertOk();
        $response->assertJson(['event' => 'transcription']);

        Queue::assertPushed(SyncVideoTranscription::class, function ($job) use ($video) {
            return $job->video->id === $video->id;
        });
    });
});

describe('BunnyStreamService VTT Parsing', function () {
    it('parses VTT content to plain text', function () {
        $vttContent = <<<'VTT'
WEBVTT

00:00:00.000 --> 00:00:05.000
Hello world, this is the first caption.

00:00:05.000 --> 00:00:10.000
And this is the second caption.

00:00:10.000 --> 00:00:15.000
<v Speaker>Finally, the third one.</v>
VTT;

        $service = app(BunnyStreamService::class);
        $transcript = $service->parseVttToTranscript($vttContent);

        expect($transcript)->toContain('Hello world');
        expect($transcript)->toContain('second caption');
        expect($transcript)->toContain('Finally, the third one');
        expect($transcript)->not->toContain('WEBVTT');
        expect($transcript)->not->toContain('-->');
    });
});

describe('Admin Video Manager Transcription', function () {
    it('shows combined transcription status', function () {
        $user = User::factory()->create();
        Video::factory()->transcribing()->create(['title' => 'Transcribing Video']);
        Video::factory()->transcribed()->create(['title' => 'Transcribed Video']);

        actingAs($user);

        Livewire::test('admin.video-manager')
            ->assertOk()
            ->assertSee('Transcribing Video')
            ->assertSee('Transcribed Video')
            ->assertSee('Transcribing')
            ->assertSee('Ready');
    });

    it('can trigger retranscription', function () {
        Queue::fake();

        $user = User::factory()->create();
        $video = Video::factory()->transcribed()->create();

        actingAs($user);

        Livewire::test('admin.video-manager')
            ->call('retranscribe', $video->id);

        Queue::assertPushed(TriggerVideoTranscription::class, function ($job) use ($video) {
            return $job->video->id === $video->id;
        });
    });

    it('creates video with filename-based title', function () {
        Http::fake([
            'video.bunnycdn.com/*' => Http::response([
                'guid' => 'new-video-123',
                'title' => 'my-awesome-video.mp4',
            ], 200),
        ]);

        $user = User::factory()->create();
        actingAs($user);

        Livewire::test('admin.video-manager')
            ->call('createVideo', 'my-awesome-video.mp4', 'videos/2026/01/test.mp4');

        $video = Video::where('bunny_video_id', 'new-video-123')->first();
        expect($video)->not->toBeNull();
        expect($video->title)->toBe('My Awesome Video');
    });
});

describe('Public Video Page with Chapters', function () {
    it('displays chapters when available', function () {
        $video = Video::factory()->transcribed()->create([
            'slug' => 'video-with-chapters',
            'is_published' => true,
            'chapters' => [
                ['title' => 'Getting Started', 'start' => 0, 'end' => 60],
                ['title' => 'Advanced Topics', 'start' => 60, 'end' => 180],
            ],
        ]);

        $response = get('/video/video-with-chapters');

        $response->assertOk();
        $response->assertSee('Chapters');
        $response->assertSee('Getting Started');
        $response->assertSee('Advanced Topics');
    });

    it('does not display chapters section when none exist', function () {
        $video = Video::factory()->create([
            'slug' => 'video-without-chapters',
            'is_published' => true,
            'chapters' => null,
        ]);

        $response = get('/video/video-without-chapters');

        $response->assertOk();
        $response->assertDontSee('Chapters');
    });
});
