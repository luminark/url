<?php 

namespace Luminark\Url\Traits;

use Luminark\Url\Models\Url;

trait HandlesUrlTrait 
{
    public function getUrlResource($uri)
    {
        $url = $this->findUrlByUri($uri);
        if ($url->redirectsTo) {
            while ($url->redirectsTo) {
                $url = $url->redirectsTo;
            }

            return redirect($url->url);
        }

        return $this->getResourceResponse($url);
    }
    
    protected function findUrlByUri($uri)
    {
        try {
            $url = Url::findOrFail($uri);
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
        
        return $url;
    }

    protected function getResourceResponse(Url $url)
    {
        return view('resource')->with('resource', $url->resource);
    }
    
}