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

use Arikaim\Core\Db\Model;

/**
 * MetaTags trait
*/
trait MetaTags 
{        
    /**
     * Get update metatags message name
     *
     * @return string
     */
    protected function getUpdateMetaTagsMessage(): string
    {
        return $this->updateMetaTagsMessage ?? 'metatags';
    }

    /**
     * Update meta tags
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function updateMetaTags($request, $response, $data) 
    {
        $data
            ->validate(true); 
      
        $uuid = $data->get('uuid');   
        $metaTitle = $data->get('meta_title');
        $metaDescription = $data->get('meta_description');   
        $metaKeywords = $data->get('meta_keywords');  

        $model = Model::create($this->getModelClass(),$this->getExtensionName())->findById($uuid);             
        if ($model == null) {
            $this->error('errors.id','Not valid modell id');
            return;
        }
    
        $result = $model->update([
            'meta_title'       => $metaTitle,
            'meta_description' => $metaDescription,
            'meta_keywords'    => $metaKeywords
        ]); 
        
        if ($result === false) {
            $this->error('errors.metatags','Error save metatags');
            return;
        }
                  
        $this
            ->message($this->getUpdateMetaTagsMessage())
            ->field('uuid',$model->uuid);   
    }
}
