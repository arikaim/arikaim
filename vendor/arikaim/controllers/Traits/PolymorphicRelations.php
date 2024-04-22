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
 * Relations trait
*/
trait PolymorphicRelations 
{        
    /**
     *  Relations model class
     *  @var string|null
     */
    protected $relationsModel = null;

    /**
     *  Relations model extension name
     *  @var string|null
     */
    protected $relationsExtension = null;

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
          
        $id = $data->get('id');
        $type = $data->get('type');
        $relationId = $data->get('relation_id');

        $model = Model::create($this->relationsModel,$this->relationsExtension);
        if ($model == null) {
            $this->error('errors.relations.add','Not valid relation model class.');
            return;
        }

        $result = $model->saveRelation($id,$type,$relationId);
      
        $this->setResponse($result,'relations.add','errors.relations.add');
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

        $uuid = $data->get('uuid');
        $id = $data->get('id');
        $type = $data->get('type');
        $relationId = $data->get('relation_id');

        $model = Model::create($this->relationsModel,$this->relationsExtension);
        if ($model == null) {
            $this->error('errors.relations.add','Not valid relation model class.');
            return;
        }
 
        if (empty($type) == false && empty($relationId) == false) {
            $result = $model->deleteRelations($id,$type,$relationId);
        } else {
            $result = $model->deleteRelation($uuid);
        }
        
        $this->setResponse($result,'relations.delete','errors.relations.delete');
    }
}
