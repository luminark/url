<?php

namespace Luminark\Url\Traits;

use Luminark\Url\Models\Url;

trait HasUrlTrait
{
    /**
     * Temporary internal variable for updated URI.
     * 
     * @var string
     */
    protected $_uri;
    
    /**
     * Url object representing the URL which points to this resource.
     * 
     * @return Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function url()
    {
        return $this->morphOne($this->getUrlClass(), 'resource');
    }
    
    /**
     * Overrides Eloquent Model's default attribute getting and gets
     * the currently set URI.
     * 
     * @return string|null URI
     */
    public function getUriAttribute()
    {
        return $this->_uri ?: ($this->url ? $this->url->uri : null);
    }
    
    /**
     * Overrides Eloquent Model's default attribute setting to store the URI
     * in memory only. Override this method if you need to have 
     * the URI stored as model attribute in the database.
     */
    public function setUriAttribute($uri)
    {
        $this->_uri = $uri;
    }
    
    /**
     * Saves the URI and related URL object for the model.
     * 
     * @return Luminark\Url\Interfaces\HasUrlInterface URL resource object
     */
    public function saveUri($uri = null)
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
        
        // Refresh the model with updated URL object
        $this->load('url');
        
        return $this;
    }
    
    /**
     * Gets the class for URL object needed to define Eloquent relationship
     * between resource and URL models. Override this method if Url class
     * is being extended.
     * 
     * @return string Url class
     */
    protected function getUrlClass()
    {
        return Url::class;
    }

    /**
     * Transforms URI value as required before storing it.
     * 
     * @return string Transformed URI value
     */
    protected function prepareUri($uri)
    {
        // Remove starting and trailing slash from URI
        $uri = preg_replace('/\/$/', '', $uri);
        $uri = preg_replace('/^\//', '', $uri);
        $uri = strtolower($uri);

        return $uri;
    }

    /**
     * Validates the URL value and makes sure it is unique. Override this 
     * method if custom validation is needed.
     * 
     * @return boolean URI validity status
     */
    protected function validateUri($uri)
    {
        $urlClass = $this->getUrlClass();
        $url = $urlClass::find($uri);
        
        return $url ? false : true;
    }

    /**
     * Modifies the $originalUrl to redirect to $newUrl.
     * 
     * @param Url $originalUrl The URL that will be redirecting
     * @param Url $newUrl The URL that will be redirected to
     */
    protected function redirectUrl(Url $originalUrl, Url $newUrl)
    {
        $newUrl->redirectsTo()->dissociate();
        $newUrl->save();
        $originalUrl->redirectsTo()->associate($newUrl);
        $originalUrl->save();
    }
}
