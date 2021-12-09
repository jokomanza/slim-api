<?php

use alsvanzelf\jsonapi\CollectionDocument;
use Slim\Http\Request;

if (!function_exists('createCollectionDocument')) {

    /**
     * @author 
     */
    function createCollectionDocument(Request $request)
    {
        $document = new CollectionDocument();

        $execution_time = (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000;
        $document->addMeta('processing_time', "$execution_time milliseconds");
        $document->addMeta('processing_time_ms', $execution_time);
        $document->setSelfLink((string) $request->getUri()->withQuery(''));

        return $document;
    }
}
