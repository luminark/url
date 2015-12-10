<?php

use Luminark\Url\Models\Url;
use Luminark\Url\Traits\HandlesUrlTrait;
use Illuminate\Routing\Controller;

class TestController extends Controller
{
    use HandlesUrlTrait;
    
    protected function getUrlResourceResponse(Url $url)
    {
        return $url->resource->title;
    }
}
