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

use Arikaim\Core\Db\Model;
use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\View\Html\Page;

/**
 * Languages controller
*/
class Language extends ApiController
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
     * Update language
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function updateController($request, $response, $data) 
    {    
        // access from contorl panel only 
        $this->requireControlPanelPermission();

        $uuid = $data->get('uuid');
        $model = Model::Language()->findById($uuid);

        $this->onDataValid(function($data) use($model,$uuid) {            
            $result = $model->update($data->toArray());
            
            $this->setResponse($result,function() use($uuid) {
                $this
                    ->message('language.update')
                    ->field('uuid',$uuid);
            },'errors.language.update');
        });

        $data
            ->addRule("exists:model=Language|field=uuid","uuid")
            ->addRule("text:min=2","title")
            ->addRule("text:min=2","native_title")
            ->addRule("unique:model=Language|field=code|exclude=" . $model->code,"code")
            ->addRule("unique:model=Language|field=code_3|exclude=" . $model->code_3,"code_3")
            ->addRule("text:min=2|max=2","language_code")
            ->validate();
    }

    /**
     * Add language
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function addController($request, $response, $data) 
    {       
        // access from contorl panel only 
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) {                   
            $model = Model::Language()->add($data->toArray());  
            
            $this->setResponse(is_object($model),function() use($model) {
                $this
                    ->message('language.add')
                    ->field('uuid',$model->uuid);
            },'errors.language.add');
        });
        $data
            ->addRule("text:min=2","title")
            ->addRule("text:min=2","native_title")
            ->addRule("unique:model=Language|field=code","code")
            ->addRule("unique:model=Language|field=code_3","code_3")
            ->addRule("text:min=2|max=2","language_code")
            ->validate();
    }

    /**
     * Remove language
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function removeController($request, $response, $data)
    {
        // access from contorl panel only 
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) {               
            $uuid = $data->get('uuid');
            $result = Model::Language()->findById($uuid)->delete();
            
            $this->setResponse($result,function() use($uuid) {
                $this
                    ->message('language.remove')
                    ->field('uuid',$uuid);
            },'errors.language.remove');
        });
        $data
            ->addRule("exists:model=Language|field=uuid","uuid")
            ->validate();        
    }
    
    /**
     * Enable/Disable language
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function setStatusController($request, $response, $data)
    {         
        // access from contorl panel only 
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) {            
            $status = $data->get('status','toggle');
            $uuid = $data->get('uuid');               
            $result = Model::Language()->findById($uuid)->setStatus($status);

            $this->setResponse($result,function() use($status,$uuid) {
                $this
                    ->message('language.status')
                    ->field('uuid',$uuid)
                    ->field('status',$status);
            },'errors.language.status');
        });
        $data
            ->addRule("exists:model=Language|field=uuid","uuid")
            ->addRule("checkList:items=0,1,toggle","status")
            ->validate();       
    }

    /**
     * Set default language
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function setDefaultController($request, $response, $data)
    {
        // access from contorl panel only 
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) {           
            $uuid = $data->get('uuid');
            $result = Model::Language()->setDefault($uuid);

            $this->setResponse($result,function() use($uuid) {
                $this
                    ->message('language.default')
                    ->field('uuid',$uuid);
            },'errors.language.default');
        });
        $data
            ->addRule("exists:model=Language|field=uuid","uuid")
            ->validate();      
    }

    /**
     *  Change language order
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function changeLanguageController($request, $response, $data)
    {
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) {
            $language = $data->get("language_code"); 
            Page::setLanguage($language);

            $this->field('language','language');
        });
        $data
            ->addRule("exists:model=Language|field=code","language_code")
            ->validate();      
    }
}
