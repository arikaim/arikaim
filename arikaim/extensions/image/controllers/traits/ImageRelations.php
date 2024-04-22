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

use Arikaim\Core\Db\Model;

/**
 * Image relations trait
*/
trait ImageRelations
{
    /**
     * Set main model image
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function setMainImage($request, $response, $data)
    {
        $data
            ->validate(true);

        $modelClass = $data->get('model_class');
        $modelExtension = $data->get('extension');
        $uuid = $data->get('uuid');
        $imageId = $data->get('image_id',null);
        $imageId = (empty($imageId) == true) ? null : $imageId;

        $model = Model::create($modelClass,$modelExtension);
        if ($model == null) {
            $this->error('Not valid model class or extension');
            return false;
        }

        $model = $model->findById($uuid);
        if ($model == null) {
            $this->error('Not valid model id');
            return false;
        }

        // check access
        $this->requireUserOrControlPanel($model->user_id ?? null);

        if ($model->setImage($imageId) == false) {
            $this->error('Error set model image');
            return false;
        };

        $imageSrc = ($model->hasImage() == true) ? $model->image->src : '/api/image/svg/view/icons~image';
        $this
            ->message('relations.main')
            ->field('uuid',$model->uuid) 
            ->field('image_id',$imageId) 
            ->field('image_src',$this->getPageUrl($imageSrc));    
    }
}
