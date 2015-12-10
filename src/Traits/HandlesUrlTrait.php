<?php 

namespace Luminark\Url\Traits;

use Luminark\Url\Models\Url;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        return $this->getUrlResourceResponse($url);
    }
    
    protected function findUrlByUri($uri)
    {
        try {
            $url = Url::findOrFail($uri);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException(null, $e);
        }
        
        return $url;
    }

    protected function getUrlResourceResponse(Url $url)
    {
        // Handle URL here
    }
    
}