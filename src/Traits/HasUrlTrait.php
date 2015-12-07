<?php

namespace Luminark\Url\Traits;

use Luminark\Url\Models\Url;

trait HasUrlTrait
{
    protected $_uri;
    
    public function url()
    {
        return $this->morphOne($this->getUrlClass(), 'resource');
    }
    
    public function getUriAttribute()
    {
        return $this->_uri ?: ($this->url ? $this->url->uri : null);
    }
    
    public function setUriAttribute($uri)
    {
        //$this->attributes['uri'] = $uri;
        $this->_uri = $uri;
    }
    
    public function updateUri($uri = null)
    {
        $urlClass = $this->getUrlClass();
        $originalUrl = $this->url;
        $uri = $uri ?: $this->uri;
        
        if ( ! is_null($uri)) {
            $uri = $this->prepareUri($uri);
            $this->validateUri($uri);
        }
        // Check if this resource object is already associated with a Url object
        if ( ! $originalUrl && ! is_null($uri)) {
            // Associate a new Url object with current resource object
            $url = $urlClass::create(['uri' => $uri]);
            $url->resource()->associate($this);
            $url->save();
        // Dissociate content from URL if uri set to null
        } elseif (is_null($uri)) {
            $originalUrl->resource()->dissociate();
            $originalUrl->save();
        // Redirect old Url object to new Url object
        } elseif ($originalUrl && $originalUrl->uri !== $uri) {
            $originalUrl->resource()->dissociate();
            $originalUrl->save();
            $newUrl = $urlClass::firstOrCreate(['uri' => $uri]);
            $this->redirectUrl($originalUrl, $newUrl);
            $newUrl->resource()->associate($this);
            $newUrl->save();
        }
        
        $this->load('url');
        
        return $this;
    }
    
    protected function getUrlClass()
    {
        return Url::class;
    }

    protected function prepareUri($uri)
    {
        // Remove starting and trailing slash from URI
        $uri = preg_replace('/\/$/', '', $uri);
        $uri = preg_replace('/^\//', '', $uri);
        $uri = strtolower($uri);

        return $uri;
    }

    /**
     * @param Content $content
     * @param $uri
     */
    protected function validateUri($uri)
    {
        $urlClass = $this->getUrlClass();
        $url = $urlClass::find($uri);
        
        return $url ? false : true;
    }

    /**
     * @param $originalUrl
     * @param $newUrl
     */
    protected function redirectUrl(Url $originalUrl, Url $newUrl)
    {
        $newUrl->redirectsTo()->dissociate();
        $newUrl->save();
        $originalUrl->redirectsTo()->associate($newUrl);
        $originalUrl->save();
    }
}