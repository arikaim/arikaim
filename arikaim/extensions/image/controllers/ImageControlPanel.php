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

use Arikaim\Core\Controllers\ControlPanelApiController;
use Arikaim\Core\Db\Model;

use Arikaim\Extensions\Image\Controllers\Traits\ImageUpload;
use Arikaim\Extensions\Image\Controllers\Traits\ImageImport;
use Arikaim\Core\Controllers\Traits\Status;

/**
 * Image contorl panel api controller
*/
class ImageControlPanel extends ControlPanelApiController
{
    use    
        ImageUpload,
        ImageImport,
        Status;

    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('image::admin.messages');
        $this->setExtensionName('image');
        $this->setModelClass('Image');
    }

    /**
     * Delete image
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function deleteController($request, $response, $data)
    { 
        $data->validate(true);

        $model = Model::Image('image')->findById($data['uuid']); 
        if ($model == null) {
            $this->error('errors.id','Not valid image id');
            return false;
        } 

        if ($model->deny_delete == true) {
            $this->error("Can't delete, image is protected.");
            return false;
        }

        $result = $model->deleteImage();
        $this->get('image.library')->deleteImageFile($model->file_name);

        $this->setResponse($result,function() use($model) {                  
            $this
                ->message('delete')
                ->field('uuid',$model->uuid);                  
        },'errors.delete');       
    }

    /**
     * Get images list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function getList($request, $response, $data)
    {
        $data
            ->validate(true);

        $search = $data->get('query','');
        $dataField = $data->get('data_field','uuid');
        $size = $data->get('size',15);
        
        $model = Model::Image('image');
        $model = $model->where('base_name','like','%' . $search . '%')->take($size)->get();
        
        $this->setResponse(($model != null),function() use($model,$dataField) {     
            $items = [];
            foreach ($model as $item) {
                $thumbnail = $item->thumbnail(64,64);
                $imageUrl = (\is_object($thumbnail) == true) ? $this->getPageUrl($thumbnail->src) : null;

                $items[] = [
                    'name'  => $item['base_name'],
                    'image' => $imageUrl,
                    'value' => $item[$dataField]
                ];
            }
            $this                    
                ->field('success',true)
                ->field('results',$items);  
        },'errors.list');
       
        return $this->getResponse(true); 
    }

    /**
     * Generate QR code
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function generateQrCodeController($request, $response, $data)
    {
        $data
            ->validate(true);

        $qrCodeData = $data->get('data','Test');
        $image = $this->get('qrcode')->render($qrCodeData);

        $this->setResponse(!empty($image),function() use($image) {                  
            $this
                ->message('qrcode')
                ->field('image',$image);                          
        },'errors.qrcode');
    }

    /**
     * Update image
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function updateController($request, $response, $data)
    {
        $data
            ->validate(true);

        $uuid = $data->get('uuid',null);
        $model = Model::Image('image')->findById($uuid);
        if ($model == null) {
            $this->error("Not valid image id.");
            return false;
        }

        $result = $model->update($data->toArray());
        $this->setResponse(($result !== false),function() use($uuid) {                  
            $this
                ->message('update')
                ->field('uuid',$uuid);                          
        },'errors.update');
    }
}
