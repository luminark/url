<?php

use Illuminate\Database\Eloquent\Model;
use Luminark\Url\Traits\HasUrlTrait;
use Luminark\Url\Interfaces\HasUrlInterface;

class Resource extends Model implements HasUrlInterface
{
    use HasUrlTrait;
    
    protected $fillable = ['title', 'uri'];
}