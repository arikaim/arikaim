<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     CoreAPI
*/
namespace Arikaim\Core\Api\Orm;

use Arikaim\Core\Controllers\ControlPanelApiController;
use Arikaim\Core\Db\Model;

/**
 * Orm relations controller
*/
class Relations extends ControlPanelApiController
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
    public function deleteRelation($request, $response, $data)
    {
        $data->validate(true);

        $model = Model::create($data['model'],$data['extension']);
        if ($model == null) {               
            $this->error('errors.relations.add','Not valid model class or extension');               
            return;
        }
        
        if (empty($data['type']) == false && empty($data['relation_id']) == false) {
            $result = $model->deleteRelations($data['id'],$data['type'],$data['relation_id']);
        } else {
            $result = $model->deleteRelation($data['uuid']);
        }
        
        $this->setResponse($result,'relations.delete','errors.relations.delete');
    }

    /**
     * Add relation
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function addRelation($request, $response, $data)
    {
        $data->validate(true);
          
        $model = Model::create($data['model'],$data['extension']);
        if ($model == null) {
            $this->error('errors.relations.add','Not valid model class or extension');
            return;
        }

        $model = $model->saveRelation($data['id'],$data['type'],$data['relation_id']);
        if ($model === false) {
            $this->error('errors.relations.add');
            return;
        }

        $this
            ->message('relations.add')
            ->field('uuid',$model->uuid);       
    }

    /**
     * Read model
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function read($request, $response, $data)
    {
        $data->validate(true);

        $model = Model::create($data['name'],$data['extension']);
        if ($model == null) {
            $this->error('errors.relations.add');
            return;
        }

        $model = $model->findById($data['uuid']);
        if ($model == null) {
            $this->error('errors.relations.read');
            return;
        }
        
        $this
            ->message('orm.read')
            ->field('data',$model->toArray());                          
    }
}
