<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Api\Orm;

use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\Db\Model;

/**
 * Orm relations controller
*/
class Relations extends ApiController
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
            $model = Model::create($data['model'],$data['extension']);
            if (is_object($model) == false) {               
                $this->error('errors.relations.add');               
                return;
            }
            $result = $model->deleteRelations($data['id'],$data['type'],$data['relation_id']);
             
            $this->setResponse($result,'relations.delete','errors.relations.delete');
        });
        $data->validate();
    }

    /**
     * Add relation
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
            if (is_object($model) == false) {
                $this->error('errors.relations.add');
                return;
            }

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
}
