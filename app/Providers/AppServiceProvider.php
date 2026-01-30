<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(PasswordReset::class, function ($event) {
            
            $event->user->update([
                'status' => 'ready',      
                'invitation_token' => null, 
                'email_verified_at' => now(), 
            ]);
            
        });
    }
}
