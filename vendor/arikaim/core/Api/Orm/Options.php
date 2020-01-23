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
 * Orm options controller
*/
class Options extends ApiController
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
     * Add option type
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function addOptionTypeController($request, $response, $data)
    {
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) { 
        });
        $data->validate();
    }

    /**
     * Update option type
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function updateOptionTypeController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) { 
        });
        $data->validate();
    }

    /**
     * Delete option type
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function deleteOptionTypeController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) { 
        });
        $data->validate();
    }

    /**
     * Add option list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function addOptionListController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) { 
        });
        $data->validate();
    }

    /**
     * Delete option list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function deleteOptionListController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) { 
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
