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
 * Orm options controller
*/
class Options extends ControlPanelApiController
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
     * Save options
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function saveOptions($request, $response, $data)
    {  
        $data->validate(true);

        $modelName = $data->get('model');
        $extension = $data->get('extension');
        $referenceId = $data->get('id');
        $model = Model::create($modelName,$extension);
        
        $result = ($model != null) ? $model->saveOptions($referenceId,$data['options']) : false;
        
        $this->setResponse($result,function() use($model) {
            $this
                ->message('orm.options.save')
                ->field('uuid',$model->uuid);                   
        },'errors.options.save');
    }
}
