<?php

namespace Database\Factories;

use App\Enums\VideoStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->randomNumber(4),
            'description' => fake()->paragraph(),
            'r2_path' => 'videos/'.fake()->uuid().'.mp4',
            'bunny_video_id' => fake()->uuid(),
            'bunny_library_id' => config('services.bunny.library_id') ?: 'test-library',
            'duration_seconds' => fake()->numberBetween(60, 3600),
            'thumbnail_url' => null,
            'status' => VideoStatus::Ready,
            'is_published' => true,
            'published_at' => now(),
            'order' => fake()->numberBetween(0, 100),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VideoStatus::Pending,
            'bunny_video_id' => null,
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VideoStatus::Processing,
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VideoStatus::Failed,
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
