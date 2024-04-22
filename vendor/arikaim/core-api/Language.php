<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     CoreAPI
*/
namespace Arikaim\Core\Api;

use Arikaim\Core\Db\Model;
use Arikaim\Core\Controllers\ControlPanelApiController;
use Arikaim\Core\Http\Cookie;
use Arikaim\Core\Http\Session;

/**
 * Languages controller
*/
class Language extends ControlPanelApiController
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
    public function update($request, $response, $data) 
    {    
        $uuid = $data->get('uuid');
        $model = Model::Language()->findById($uuid);

        $data
            ->addRule('exists:model=Language|field=uuid','uuid')
            ->addRule('text:min=2','title')
            ->addRule('text:min=2','native_title')
            ->addRule('unique:model=Language|field=code|exclude=' . $model->code,'code')
            ->addRule('unique:model=Language|field=code_3|exclude=' . $model->code_3,'code_3')
            ->addRule('text:min=2|max=2','language_code')
            ->validate(true);

        $result = $model->update($data->toArray());
        
        $this->setResponse($result,function() use($uuid) {
            $this
                ->message('language.update')
                ->field('uuid',$uuid);
        },'errors.language.update');
    }

    /**
     * Add language
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function add($request, $response, $data) 
    {       
        $data
            ->addRule('text:min=2','title')
            ->addRule('text:min=2','native_title')
            ->addRule('unique:model=Language|field=code','code',$this->getMessage('errors.language.code'))
            ->addRule('unique:model=Language|field=code_3','code_3',$this->getMessage('errors.language.code3'))
            ->addRule('text:min=2|max=2','language_code')
            ->validate(true);
      
        $model = Model::Language()->add($data->toArray());  
        if ($model == null) {
            $this->error('errors.language.add');
            return false;
        }
       
        $this
            ->message('language.add')
            ->field('uuid',$model->uuid);
    }

    /**
     * Remove language
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function remove($request, $response, $data)
    { 
        $data
            ->addRule('exists:model=Language|field=uuid','uuid')
            ->validate(true);    
 
        $uuid = $data->get('uuid');
        $result = Model::Language()->findById($uuid)->delete();
        
        $this->setResponse($result,function() use($uuid) {
            $this
                ->message('language.remove')
                ->field('uuid',$uuid);
        },'errors.language.remove'); 
    }
    
    /**
     * Enable/Disable language
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function setStatus($request, $response, $data)
    {       
        $data
            ->addRule('exists:model=Language|field=uuid','uuid')
            ->addRule('checkList:items=0,1,toggle','status')
            ->validate(true); 

        $status = $data->get('status','toggle');
        $uuid = $data->get('uuid');               
        $result = Model::Language()->findById($uuid)->setStatus($status);

        $this->setResponse($result,function() use($status,$uuid) {
            $this
                ->message('language.status')
                ->field('uuid',$uuid)
                ->field('status',$status);
        },'errors.language.status');
    }

    /**
     * Set default language
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function setDefault($request, $response, $data)
    {
        $data
            ->addRule('exists:model=Language|field=uuid','uuid')
            ->validate(true);      

        $uuid = $data->get('uuid');
        $model = Model::Language()->findById($uuid);
        if ($model == null) {
            $this->error('errors.language.default');
            return false;
        }
        
        $this->get('cache')->clear();
        $this->get('config')->setValue('settings/defaultLanguage',$model->code);
        // save and reload config file
        $result = $this->get('config')->save();
        $this->get('cache')->clear();

        $this->setResponse($result,function() use($uuid) {
            $this
                ->message('language.default')
                ->field('uuid',$uuid);
        },'errors.language.default');
    }

    /**
     *  Change current language
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function changeLanguage($request, $response, $data)
    { 
        $data
            ->addRule('exists:model=Language|field=code','language_code')
            ->validate(true);

        $language = $data->get('language_code','en');       

        Session::set('language',$language);
        Cookie::add('language',$language);

        $this->get('page')->setLanguage($language);

        $this->field('language','language');
    }
}
