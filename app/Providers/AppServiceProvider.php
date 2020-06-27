<?php

namespace App\Providers;

use App\Channel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // if ($this->app->isLocal()) {
        //     $this->app->register(DebugbarServiceProvider::class);
        // }

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function($view){
            $channels  = Cache::rememberForever('channels', function(){
                return Channel::all();
            });

            $view->with('channels', $channels);
        });

        // \View::share('channels', Channel::all());
        Validator::extend('spamfree', 'App\Rules\SpamFree@passes');
    }
}
