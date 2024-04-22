<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Category\Controllers;

use Arikaim\Core\Db\Model;
use Arikaim\Core\Controllers\ControlPanelApiController;

/**
 * Category control panel controler
*/
class CategoryControlPanel extends ControlPanelApiController
{

    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('category::admin.messages');
    }
    
    /**
     * Add new category
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function addController($request, $response, $data) 
    {       
        $data
            ->addRule('text:min=2','title')           
            ->validate(true);     

        $category = Model::Category('category');   
        $title = $data->get('title',null);  
        $data['parent_id'] = (empty($data['parent_id']) === true) ? null : $data['parent_id'];
        
        if ($category->hasCategory($title,$data['parent_id']) == true) {
            $this->error('errors.exist');
            return false;
        } 
          
        $model = $category->create($data->toArray());

        $this->setResponse(($model !== false),function() use($model,$data) {                                                       
            $this->get('event')->dispatch('category.create',$model->toArray());            
            $this
                ->message('add')
                ->field('id',$model->id)              
                ->field('uuid',$model->uuid);           
        },'errors.add'); 
    }

    /**
     * Save category dscription
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function updateDescriptionController($request, $response, $data) 
    {
        $data
            ->validate(true); 

        $model = Model::Category('category')->findByid($data['uuid']);   
        if ($model == null) {
            $this->error("Not valid cactegory id");
            return false;
        }

        $result = $model->update([
            'description'   => $data['description']
        ]); 
                
        $this->setResponse(($result !== false),function() use($model) {
            $this->get('event')->dispatch('category.update',['uuid' => $model->uuid]);   
            $this
                ->message('update')
                ->field('uuid',$model->uuid);   
        },'errors.update');  
    }

    /**
     * Update category meta tags
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function updateMetaTagsController($request, $response, $data) 
    {
        $data                            
            ->validate(true); 
    
        $model = Model::Category('category')->findByid($data['uuid']);  
        if ($model == null) {
            $this->error("Not valid cactegory id");
            return false;
        }

        $result = $model->update([
            'meta_title'         => $data['meta_title'],
            'meta_description'   => $data['meta_description'],
            'meta_keywords'      => $data['meta_keywords'],
        ]); 
 
        $this->setResponse(($result !== false),function() use($model) {
            $this->get('event')->dispatch('category.update',['uuid' => $model->uuid]);   
            $this
                ->message('update')
                ->field('uuid',$model->uuid);   
        },'errors.update');  
    }

    /**
     * Update category
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function updateController($request, $response, $data) 
    {    
        $data      
            ->addRule('text:min=2','title')        
            ->validate(true);    

      
        $uuid = $data->get('uuid');
      
        if (isset($data['image_id']) == true) {
            $data['image_id'] = (empty($data['image_id']) == true) ? null : $data['image_id'];
        }
        $model = Model::Category('category')->findByid($uuid);
        if ($model == null) {
            $this->error('Not valid id');
            return false;
        }

        // save parent id           
        $data['parent_id'] = (empty($data['parent_id']) == true) ? null : $data['parent_id'];    
              
        $result = $model->update($data->toArray());     
            
        $this->setResponse(($result !== false),function() use($model) {
            $this->get('event')->dispatch('category.update',['uuid' => $model->uuid]);   
            $this
                ->message('update')
                ->field('uuid',$model->uuid);   
        },'errors.update');
    }
  
    /**
     * Delete category
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function deleteController($request, $response, $data)
    { 
        $this->onDataValid(function($data) {
            $uuid = $data->get('uuid');
            $result = Model::Category('category')->remove($uuid);

            $this->setResponse($result,function() use($uuid) {
                $this->get('event')->dispatch('category.delete',['uuid' => $uuid]); 
                $this
                    ->message('delete')
                    ->field('uuid',$uuid);  
            },'errors.delete');
        }); 
        $data->validate();
    }
      
    /**
     * Enable/Disable category
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function setStatusController($request, $response, $data)
    {
        $data
            ->validate(true); 

        $status = $data->get('status',1);                
        $uuid = $data->get('uuid');
        $model = Model::Category('category')->findById($uuid);
        $result = $model->setStatus($status); 
        $model->setChildStatus($uuid,$status);

        $this->setResponse($result,function() use($uuid,$status,$data) {             
            $this->get('event')->dispatch('category.status',$data->toArray());  
            $this
                ->message('status')
                ->field('uuid',$uuid)
                ->field('status',$status);
        },'errors.status');
    }
}
