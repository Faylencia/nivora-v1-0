<?php

namespace App\Providers;

use App\Services\AiClientInterface;
use App\Services\DisabledAiClient;
use App\Services\GeminiClient;
use App\Services\OpenAiClient;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AiClientInterface::class, function (): AiClientInterface {
            return match ((string) config('services.ai.provider', 'gemini')) {
                'openai' => OpenAiClient::fromConfig(),
                'gemini' => GeminiClient::fromConfig(),
                default => new DisabledAiClient,
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('auth', function (Request $request): Limit {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('ai', function (Request $request): Limit {
            return Limit::perHour(20)->by((string) ($request->user()?->getAuthIdentifier() ?? $request->ip()));
        });
    }
}
