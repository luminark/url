<?php

namespace Luminark\Url;

use Illuminate\Support\ServiceProvider;
use Luminark\Url\Interfaces\HasUrlInterface;

class UrlServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }
    
    public function register()
    {
        $this->app['events']->listen('eloquent.saved*', function ($model) {
            if ($model instanceof HasUrlInterface) {
                $model->saveUri();
            }
        });
        
        $this->app['events']->listen('eloquent.deleted*', function ($model) {
            if ($model instanceof HasUrlInterface && $model->url) {
                $model->url->delete();
            }
        });
    }
}
