<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->longText('transcript')->nullable();
            $table->string('transcript_language', 10)->default('en');
            $table->json('chapters')->nullable();
            $table->json('moments')->nullable();
            $table->string('transcription_status')->default('pending');
            $table->timestamp('transcribed_at')->nullable();
            $table->json('captions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn([
                'transcript',
                'transcript_language',
                'chapters',
                'moments',
                'transcription_status',
                'transcribed_at',
                'captions',
            ]);
        });
    }
};
