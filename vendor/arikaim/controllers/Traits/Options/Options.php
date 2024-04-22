<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Controllers\Traits\Options;

use Arikaim\Core\Db\Model;

/**
 * Orm options trait
*/
trait Options
{   
    /**
     * Save options
     *
     * @Api(
     *      description="Save options",    
     *      parameters={
     *          @ApiParameter (name="id",type="integer",description="Options ref Id",required=true),
     *          @ApiParameter (name="options",type="array",description="Options values",required=true)                   
     *      }
     * )
     * 
     * @ApiResponse(
     *      fields={
     *          @ApiParameter (name="uuid",type="string",description="Model uuid")
     *      }
     * )  
     * 
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function saveController($request, $response, $data)
    {  
        $data->validate(true);

        $referenceId = $data->get('id');
        $options = $data->get('options',[]);
        $model = Model::create($this->getModelClass(),$this->getExtensionName());
        if ($model == null) {
            $this->error('errors.id');
            return;
        }

        $result = $model->saveOptions($referenceId,$options);
        
        $this->setResponse($result,function() use($model) {
            $this
                ->message('orm.options.save')
                ->field('uuid',$model->uuid);                   
        },'errors.options.save');
    }

    /**
     * Save single option
     *
     * @Api(
     *      description="Save option",    
     *      parameters={
     *          @ApiParameter (name="id",type="integer",description="Option ref Id",required=true),
     *          @ApiParameter (name="key",type="string",description="Option key name",required=true),
     *          @ApiParameter (name="value",type="mixed",description="Option value",required=true)                      
     *      }
     * )
     * 
     * @ApiResponse(
     *      fields={
     *          @ApiParameter (name="uuid",type="string",description="Model uuid")
     *      }
     * ) 
     * 
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function saveOptionController($request, $response, $data)
    {  
        $data->validate(true);

        $referenceId = $data->get('id');
        $key = $data->get('key',null);
        $value = $data->get('value',null);

        $model = Model::create($this->getModelClass(),$this->getExtensionName());
        if ($model == null) {
            $this->error('errors.id');
            return;
        }

        $result = $model->saveOption($referenceId,$key,$value);
        
        $this->setResponse($result,function() use($model) {
            $this
                ->message('orm.options.save')
                ->field('uuid',$model->uuid);                   
        },'errors.options.save');
    }
}
