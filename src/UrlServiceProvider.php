<?php

namespace Luminark\Url;

use Illuminate\Support\ServiceProvider;
use Luminark\Url\Interfaces\HasUrlInterface;

class UrlServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Handle config here
    }
    
    public function register()
    {
        $this->app['events']->listen('eloquent.saved*', function ($model) {
            if ($model instanceof HasUrlInterface) {
                $model->updateUri();
            }
        });
        
        //TODO Handle deleting
    }
}