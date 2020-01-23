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

use Arikaim\Core\Utils\Path;

/**
 * File upload trait
*/
trait FileUpload 
{        
    /**
     * Soft delete model
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function uploadController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) use ($request) {                
            $files = $request->getUploadedFiles();

            if (isset($files['file']) == true) {
                $file = $files['file'];
            } else {
                $this->error('errors.upload');
                return;
            }

            $result = false;
            if ($file->getError() === UPLOAD_ERR_OK) {
                $path = Path::STORAGE_PATH . $data->get('path','');
                $fileName = $path . $file->getClientFilename();
                $file->moveTo($fileName);
                $result = $file->isMoved();
            }
               
            $this->setResponse($result,function() use($file) {                  
                $this
                    ->message('upload')
                    ->field('file_name',$file->getClientFilename());                                  
            },'errors.upload');           
        });
        $data->validate();          
    }
}
