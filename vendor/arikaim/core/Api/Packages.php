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
use Arikaim\Core\Packages\PackageManager;
use Arikaim\Core\Db\Model;
use Arikaim\Core\View\Theme;
use Arikaim\Core\View\Template\Template;

/**
 * Packages controller
*/
class Packages extends ApiController
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
     * Dowload and install package from repository
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function repositoryInstallController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {            
            $type = $data->get('type',null);
            $name = $data->get('package',null);
            $packageManager = $this->get('packages')->create($type);

            if ($type != PackageManager::LIBRARY_PACKAGE) {
                // create package backup
                $packageManager->createBackup($name);
            }
        
            $repository = $packageManager->getRepository($name);
            $result = (is_object($repository) == true) ? $repository->install() : false;
            
            $this->setResponse($result,function() use($name,$type) {  
                // clear cache    
                $this->get('cache')->clear();            
                $this
                    ->message($type . '.install')
                    ->field('type',$type)   
                    ->field('name',$name);                  
            },'errors.' . $type . '.install');
        });
        $data->validate();       
    }

    /**
     * Uninstall package
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function unInstallController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) { 
            $type = $data->get('type',null);
            $name = $data->get('name',null);

            $packageManager = $this->get('packages')->create($type);
            $result = $packageManager->unInstallPackage($name);

            $this->setResponse($result,function() use($name,$type) {                  
                $this
                    ->message($type . '.uninstall')
                    ->field('type',$type)   
                    ->field('name',$name);                  
            },'errors.' . $type . '.uninstall');
        });
        $data->validate();
    }

    /**
     * Install package
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function installController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) { 
            $type = $data->get('type',null);
            $name = $data->get('name',null);

            $packageManager = $this->get('packages')->create($type);
            $result = $packageManager->installPackage($name);

            $this->setResponse($result,function() use($name,$type) {                  
                $this
                    ->message($type . '.install')
                    ->field('type',$type)   
                    ->field('name',$name);                  
            },'errors.' . $type . '.install');
        });
        $data->validate();
    }

    /**
     * Update (reinstall) package
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function updateController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {  
            $type = $data->get('type',null);
            $name = $data->get('name',null);

            $packageManager = $this->get('packages')->create($type);            
            $package = $packageManager->createPackage($name);

            $package->unInstall();
            $result = $package->install();

            $this->setResponse($result,function() use($name,$type) {
                $this
                    ->message($type . '.update')
                    ->field('type',$type)   
                    ->field('name',$name);         
            },'errors.' . $type  . '.update');
        });
        $data->validate();
    }

    /**
     * Enable/Disable package
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setStatusController($request, $response, $data)
    {
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) {     
            $type = $data->get('type',null);
            $name = $data->get('name',null);
            $status = $data->get('status',1);

            $packageManager = $this->get('packages')->create($type);            
          
            $result = ($status == 1) ? $packageManager->enablePackage($name) : $packageManager->disablePackage($name);
            $stausLabel = ($status == 1) ? 'enable' : 'disable';

            $this->setResponse($result,function() use($name,$type,$status,$stausLabel) {               
                $this
                    ->message($type . '.' . $stausLabel)
                    ->field('type',$type)   
                    ->field('status',$status)
                    ->field('name',$name);         
            },'errors.' . $type  . '.' . $stausLabel);
        });
        $data->validate();
    }

    /**
     * Save module config
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function saveConfigController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {            
            $module = Model::Modules()->FindByColumn('name',$data['name']);
            $module->config = $data->toArray();
            $result = $module->save();

            $this->setResponse($result,'module.config','errors.module.config');
        });
        $data->validate();       
    }

    /**
     * Set current theme
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setCurrentThemeController($request, $response, $data)
    {       
        // access from contorl panel only 
        $this->requireControlPanelPermission();
            
        $this->onDataValid(function($data) {
            $themeName = $data->get('theme_name');
            $templateName = $data->get('template_name',Template::getTemplateName());          
            Theme::setCurrentTheme($themeName,$templateName);
         
            $this
                ->message('theme.current')
                ->field('theme',$themeName)
                ->field('template',$templateName);
        });
        $data->validate();
    }

    /**
     * Set current template
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setCurrentController($request, $response, $data)
    {       
        // access from contorl panel only 
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) { 
            $name = $data['name'];
            $current = Template::getTemplateName();
            $packageManager = $this->get('packages')->create('template');            

            // uninstall current template routes 
            $package = $packageManager->createPackage($current);
            $result = $package->unInstall();
          
            // install new template routes
            $package = $packageManager->createPackage($name);
            $result = $package->install($name);

            $this->setResponse($result,function() use($name) {         
                Template::setTemplateName($name);      
                $this
                    ->message('template.current')
                    ->field('name',$name);         
            },'errors.template.current'); 
        });
        $data->validate();            
    }

    /**
     * Set ui library params
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setLibraryParamsController($request, $response, $data)
    {       
        // access from contorl panel only 
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) { 
            $name = $data['name'];
            $libraryParams = json_decode($data->get('params'),true);
            $result = [];
        
            foreach ($libraryParams as $item) {
                $result[$item['name']] = $item['value'];
            }

            $params = $this->get('options')->get('library.params',[]);
            $params[$name] = $result;
            $result = $this->get('options')->set('library.params',$params);
          
            $this->setResponse($result,function() use($name) {                        
                $this
                    ->message('library.params')
                    ->field('name',$name);         
            },'errors.library.params'); 
        });
        $data->validate();            
        
    }
}
