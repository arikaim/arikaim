<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Image\Controllers\Traits;

use Arikaim\Core\Controllers\Traits\FileUpload;

/**
 * Image upload trait
*/
trait ImageUpload
{
    use FileUpload;

    /**
     * Upload image
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function upload($request, $response, $data) 
    {          
        $data->validate(true);   

        $fileName = $data->get('file_name',null);      
        $denyDelete = $data->getString('deny_delete',null);   
        $private = $data->getBool('private_image',false);                                     
        $destinationPath = $data->get('target_path',null);     
        $relationId = $data->get('relation_id',null);
        $relationType = $data->get('relation_type',null);
        $thumbnailWidth = $data->get('thumbnail_width',null);
        $thumbnailHeight = $data->get('thumbnail_height',null);
        $resizeWidth = $data->get('resize_width',null);
        $resizeHeight = $data->get('resize_height',null);
        $categoryId = $data->get('category_id',null);
        $imageId = $data->get('image_id',null);   
        $collection = $data->get('collection',null);
        $userId = $this->getUserId();

        $destinationPath = $this->get('image.library')->createImagesPath($userId,$private,$destinationPath);
        $fullPath = $this->get('storage')->getFullPath($destinationPath);

        $files = $this->uploadFiles($request,$fullPath,false,true,$fileName);
           
        // process uploaded files        
        foreach ($files as $item) {               
            if (empty($item['error']) == false) {
                $this->error('Error upload image');
                return false;
            };
            
            if (empty($resizeWidth) == false && empty($resizeHeight) == false) {
                $image = $this->get('image.library')->resizeAndSave($destinationPath . $item['name'],$userId,
                $resizeWidth,
                $resizeHeight,[
                    'private'     => $private,
                    'category_id' => (empty($categoryId) == true) ? null : $categoryId,
                    'deny_delete' => empty($denyDelete) ? 0 : 1,
                    'image_id'    => $imageId
                ],$private);  
            } else {
                $image = $this->get('image.library')->save($destinationPath . $item['name'],$userId,[
                    'private'     => $private,
                    'category_id' => (empty($categoryId) == true) ? null : $categoryId,
                    'deny_delete' => empty($denyDelete) ? 0 : 1,
                    'image_id'    => $imageId
                ],$private); 
            }                             
        }
        
        if ($image == null) {
            $this->error('errors.upload','Error upload image');
            return false;
        }

        if (empty($relationId) == false && empty($relationType) == false) {
            // add relation
            $this->get('image.library')->saveRelation($image,$relationId,$relationType);
        }

        if (empty($thumbnailWidth) == false && empty($thumbnailHeight) == false) {
            // create thumbnail
            $this->get('image.library')->createThumbnail($image,$thumbnailWidth,$thumbnailHeight);
        }
            
        if (empty($collection) == false) {
            $collectionModel = $this->get('image.library')->saveCollection($collection);
            if ($collectionModel !== false) {
                $this->get('image.library')->addImageToCollection($image,$collectionModel);
            }
        }

        // fire event 
        $this->get('event')->dispatch('image.upload',\array_merge($image->toArray(),$data->toArray()));
        $this
            ->message('upload')
            ->field('uuid',$image->uuid)
            ->field('id',$image->id)
            ->field('image_src',$image->src)
            ->field('private',$private)
            ->field('file',$image->file_name);                                  
    }
}
