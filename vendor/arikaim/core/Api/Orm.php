<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Api;

use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\Db\Model;

/**
 * Orm controller
*/
class Orm extends ApiController
{   
    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('system:admin.messages');
    }

    /**
     * Remove relation
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function deleteRelationController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {            
            $model = Model::create($data['model'],$data['extension'])->findByid($data['uuid']);
            $result = (is_object($model) == true) ? $model->delete() : false;

            $this->setResponse($result,'relations.delete','errors.relations.delete');
        });
        $data->validate();
    }

    /**
     * Remove relation
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function addRelationController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {                        
            $model = Model::create($data['model'],$data['extension']);
            $result = $model->saveRelation($data['id'],$data['type'],$data['relation_id']);
            
            $this->setResponse($result,'relations.add','errors.relations.add');
        });
        $data->validate();
    }

    /**
     * Read model
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function readController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {                        
            $model = Model::create($data['name'],$data['extension']);
            $model = (is_object($model) == true) ? $model->findById($data['uuid']) : null;
  
            $this->setResponse(is_object($model),function() use($model) {
                $this
                    ->message('orm.read')
                    ->field('data',$model->toArray());                   
            },'errors.orm.read');
        });
        $data->validate();
    }

    /**
     * Save options
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function saveOptionsController($request, $response, $data)
    {
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) { 
            $modelName = $data->get('model');
            $extension = $data->get('extension');
            $referenceId = $data->get('id');
            $model = Model::create($modelName,$extension);
            
            $result = (is_object($model) == true) ? $model->saveOptions($referenceId,$data['options']) : false;
         
            $this->setResponse($result,function() use($model) {
                $this
                    ->message('orm.options.save')
                    ->field('uuid',$model->uuid);                   
            },'errors.options.save');
        });

        $data->validate();
    }
}
