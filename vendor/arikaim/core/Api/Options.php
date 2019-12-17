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

/**
 * System options controller
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
     * Save option
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function saveController($request, $response, $data) 
    {                
        // access from contorl panel only 
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) { 
            $extensionName = $data->get('extension_name',null);
            $autoLoad = $data->get('auto_load',false);
            $key = $data->get('key');
            $value = $data->get('value');

            $result = $this->get('options')->set($key,$value,$autoLoad,$extensionName);

            $this->setResponse($result,function() use($key,$value) {
                $this
                    ->message('options.save')
                    ->field('key',$key)
                    ->field('value',$value);
            },'errors.options.save');          
        });
        $data
            ->addRule("text:min=2","key")
            ->validate();      
    }

    /**
     * Get option
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function getController($request, $response, $data) 
    {                          
        $this->onDataValid(function($data) { 
            $value = $this->get('options')->get($data['key']);
            $this->setResult($value);           
        });
        $data
            ->addRule("exists:model=Options|field=key","key")
            ->validate();  
    }
    
    /**
     *  Save options
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function saveOptionsController($request, $response, $data) 
    {    
        // access from contorl panel only 
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {           
            $extensionName = $data->get('extension_name',null);
            $autoLoad = $data->get('auto_load',false);

            foreach ($data as $key => $value) {
                $this->get('options')->set($key,$value,$autoLoad,$extensionName);
            }
            $this->message('options.save');
        });

        $data->validate();
    }
}
