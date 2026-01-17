<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyBunnyWebhookSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('X-Bunny-Signature', '');
        $payload = $request->getContent();
        $secret = config('services.bunny.webhook_secret');

        if (empty($secret)) {
            Log::error('Bunny webhook rejected: BUNNY_WEBHOOK_SECRET is not configured');

            return response()->json(['error' => 'Webhook not configured'], 500);
        }

        $expectedSignature = hash('sha256', $payload.$secret);

        if (! hash_equals($expectedSignature, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        return $next($request);
    }
}
