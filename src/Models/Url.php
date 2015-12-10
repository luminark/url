<?php

namespace Luminark\Url\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Exception;

class Url extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = ['uri', 'redirects_to'];

    protected $primaryKey = 'uri';

    protected $with = ['resource', 'redirectsTo'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Url $url) {
            $url->created_at = Carbon::now();
        });
        static::deleting(function (Url $url) {
            if ($url->redirectedToBy) {
                $url->redirectedToBy->each(function (Url $url) {
                    $url->delete();
                });
            }
        });
    }

    public function resource()
    {
        return $this->morphTo();
    }

    public function redirectsTo()
    {
        return $this->belongsTo(static::class, 'redirects_to', 'uri');
    }

    public function redirectedToBy()
    {
        return $this->hasMany(static::class, 'redirects_to', 'uri');
    }
    
    public function setUriAttribute($uri)
    {
        if ($this->exists) {
            throw new Exception(
                "An existing Url object's uri parameter cannot be updated, update the resource's uri attribute instead."
            );
        }
        
        $this->attributes['uri'] = $uri;
    }
    
    public function getUrlAttribute()
    {
        return '/' . $this->uri;
    }

    public function __toString()
    {
        return $this->url;
    }
}
