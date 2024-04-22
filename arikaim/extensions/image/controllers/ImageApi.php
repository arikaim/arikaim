<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Image\Controllers;

use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\Db\Model;

use Arikaim\Core\Utils\File;
use Arikaim\Extensions\Image\Controllers\Traits\ImageUpload;
use Arikaim\Extensions\Image\Controllers\Traits\ImageImport;
use Arikaim\Extensions\Image\Controllers\Traits\ViewSvg;
use Arikaim\Extensions\Image\Controllers\Traits\DownloadImage;
use Arikaim\Extensions\Image\Controllers\Traits\ImageRelations;
use Arikaim\Core\Controllers\Traits\FileDownload;

/**
 * Image api controller
*/
class ImageApi extends ApiController
{
    use       
        ImageUpload,
        ImageImport,
        ImageRelations,
        ViewSvg,
        DownloadImage,
        FileDownload;

    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('current>images.messages');
    }

    /**
     * View protected image
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function view($request, $response, $data) 
    {            
        $uuid = $data->get('uuid',null);
        $image = Model::Image('image')->findById($uuid);
        // not valid image uuid or id 
        if ($image == null) {
            $this->error('errors.id','Not valid image id.');
            return false;
        }
  
        if ($image->status == $image->DISABLED()) {
            return $this->viewSvg($request,$response,$data);
        }

        $mimeType = ($image->mime_type == 'image/svg') ? 'image/svg+xml' : null;
        
        if ($this->get('storage')->has($image->file_name,'storage') == true) {
            return $this->viewImage($response,$image->file_name,'storage',$mimeType);
        }

        if (File::exists($image->file_name) == true) {
            $mimeType = $mimeType ?? File::getMimetype($image->file_name);
            return $this->viewImage($response,$image->file_name,null,$mimeType);
        }

        // get full path 
        $imageFile = $this->get('storage')->getFullPath($image->file_name);
        if (File::exists($imageFile) == true) {
            $mimeType = $mimeType ?? File::getMimetype($imageFile);
            return $this->viewImage($response,$imageFile,null,$mimeType);
        }

        return $this->viewSvg($request,$response,$data);
    } 
    
    /**
     * View protected image thumbnail
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function viewThumbnail($request, $response, $data) 
    {            
        $uuid = $data->get('uuid',null);
        $thumbnail = Model::ImageThumbnails('image')->findById($uuid);
        // not valid image thumbnail uuid or id 
        if ($thumbnail == null) {
            $this->error('errors.id','Not valid image id.');
            return false;
        }
  
        return $this->viewImage($response,$thumbnail->src);
    } 

    /**
     * Set image status
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function setStatus($request, $response, $data) 
    {   
        $uuid = $data->get('uuid',null);
        $status = $data->getInt('status',0);
        $image = Model::Image('image')->findById($uuid);
        // not valid image id
        if ($image == null) {
            $this->error('errors.id','Not valid image id.');
            return false;
        }

        // check for image user match to current logged user
        $this->requireUser($image->user_id);
       
        $result = $image->setStatus($status);

        $this->setResponse($result,function() use($image,$status) {                  
            $this
                ->message('status')
                ->field('uuid',$image->uuid) 
                ->field('file_name',$image->file_name)  
                ->field('uuid',$status);                  
        },'errors.status');
    }

    /**
     * Delete image
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function delete($request, $response, $data) 
    {   
        $data
            ->validate(true);

        $uuid = $data->get('uuid',null);
        $image = Model::Image('image')->findById($uuid);
        // not valid image id
        if ($image == null) {
            $this->error('Not valid image id.');
            return false;
        }

        // check for image user match to current logged user
        $this->requireUser($image->user_id);
      
        if ($image->deny_delete == true) {
            $this->error("Can't delete, image is protected.");
            return false;
        }

        $result = $image->deleteImage();
        $this->get('image.library')->deleteImageFile($image->file_name);

        $this->setResponse($result,function() use($image) {                  
            $this
                ->message('delete')
                ->field('uuid',$image->uuid) 
                ->field('file_name',$image->file_name);            
        },'errors.delete');
    }
}
