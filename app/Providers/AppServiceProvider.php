<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Relation::morphMap([
            'post'    => 'App\Models\Content\Post',
            'comment' => 'App\Models\Content\Comment',
        ]);

        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        $isTesting = app()->environment('local', 'testing');

        RateLimiter::for('login', function (Request $request) use ($isTesting) {
            if ($isTesting) return Limit::none();
            return [
                Limit::perMinute(5)->by($request->ip()),
                Limit::perMinute(3)->by($request->input('email')),
            ];
        });

        RateLimiter::for('register', function (Request $request) use ($isTesting) {
            if ($isTesting) return Limit::none();
            return Limit::perMinute(3)->by($request->ip());
        });

        RateLimiter::for('forgot-password', function (Request $request) use ($isTesting) {
            if ($isTesting) return Limit::none();
            return Limit::perMinute(3)->by($request->ip());
        });

        RateLimiter::for('api-public', function (Request $request) use ($isTesting) {
            if ($isTesting) return Limit::none();
            return Limit::perMinute(100)->by($request->ip());
        });

        RateLimiter::for('api-protected', function (Request $request) use ($isTesting) {
            if ($isTesting) return Limit::none();
            return Limit::perMinute(150)->by($request->user()?->id ?: $request->ip());
        });
    }
}