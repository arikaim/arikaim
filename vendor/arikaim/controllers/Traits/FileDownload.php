<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Controllers\Traits;

/**
 * File download trait
*/
trait FileDownload 
{            
    /**
     * Set response headers
     *
     * @param object $response
     * @param string $fileName
     * @param \Slim\Http\Stream $stream
     * @return object
     */
    public function downloadFileHeaders($response, $fileName, $stream)
    {
        return $response
            ->withHeader('Content-Type','application/force-download')
            ->withHeader('Content-Type','application/octet-stream')
            ->withHeader('Content-Type','application/download')
            ->withHeader('Content-Description','File Transfer')
            ->withHeader('Content-Transfer-Encoding','binary')
            ->withHeader('Content-Disposition','attachment; filename="' . basename($fileName) . '"')
            ->withHeader('Expires','0')
            ->withHeader('Cache-Control','must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma','public')
            ->withBody($stream); 
    }    
}
