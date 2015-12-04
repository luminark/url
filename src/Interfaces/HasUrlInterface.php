<?php

namespace Luminark\Url\Interfaces;

interface HasUrlInterface
{
    public function url();
    
    public function getUriAttribute();
    
    public function setUriAttribute($uri);
}