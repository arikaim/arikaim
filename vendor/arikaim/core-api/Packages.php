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

use Arikaim\Core\Controllers\ControlPanelApiController;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Packages\Composer;

/**
 * Packages controller
*/
class Packages extends ControlPanelApiController
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
     * Uninstall package
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function unInstall($request, $response, $data)
    {
        $data->validate(true);

        $this->get('cache')->clear();

        $type = $data->get('type',null);
        $name = $data->get('name',null);

        $packageManager = $this->get('packages')->create($type);
        $result = $packageManager->unInstallPackage($name);

        if (\is_array($result) == true) {
            $this->addErrors($result);
            return;
        }

        $this->setResponse($result,function() use($name,$type) {                  
            $this
                ->message($type . '.uninstall')
                ->field('type',$type)   
                ->field('name',$name);                  
        },'errors.' . $type . '.uninstall');
    }

    /**
     * Update or Install composer packages
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function updateComposerPackages($request, $response, $data)
    {
        $data->validate(true);

        $this->get('cache')->clear();
        
        $type = $data->get('type',null);
        $name = $data->get('name',null);

        $packageManager = $this->get('packages')->create($type);
        $package = $packageManager->createPackage($name);
    
        if ($package == null) {
            $this->error('errors.package.name');
            return;
        }
        $require = $package->getRequire();
        $composerPackages = $require->get('composer',[]);
        
        foreach ($composerPackages as $packageName) {
            if (Composer::isInstalled($packageName) === false) {
                Composer::requirePackage($packageName);     
            } else {
                Composer::updatePackage($packageName);     
            }                         
        }    
        $result = (bool)Composer::isInstalled($composerPackages);
    
        $this->setResponse($result,function() use($name,$type) {                  
            $this
                ->message('composer.update')
                ->field('type',$type)   
                ->field('name',$name);                  
        },'errors.composer.update');
    }

    /**
     * Install package
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function install($request, $response, $data)
    {
        $data->validate(true);

        $this->get('cache')->clear();
        
        $type = $data->get('type',null);
        $name = $data->get('name',null);
        $runPostInstall = $data->get('run_post_install',true);

        $packageManager = $this->get('packages')->create($type);
        $result = $packageManager->installPackage($name);
        if (\is_string($result) == true) {
            $this->error($result);
            return false;
        }

        if (\is_array($result) == true) {
            $this->addErrors($result);
            return;
        }
        
        if ($runPostInstall == true) {               
            // post install actions
            $packageManager->postInstallPackage($name);
        }
        
        $this->setResponse($result,function() use($name,$type) {                  
            $this
                ->message($type . '.install')
                ->field('type',$type)   
                ->field('name',$name);                  
        },'errors.' . $type . '.install');
    }

    /**
     * Update (reinstall) package
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function update($request, $response, $data)
    {
        $data->validate(true);

        $this->get('cache')->clear();
    
        $type = $data->get('type',null);
        $name = $data->get('name',null);
        $runPostInstall = $data->get('run_post_install',true);

        $packageManager = $this->get('packages')->create($type);            
        $package = $packageManager->createPackage($name);
        if ($package == null) {
            $this->error('errors.package.name');
            return;
        }

        $properties = $package->getProperties();
        $primary = $properties->get('primary',false);

        $package->unInstall();    
        
        $this->get('cache')->clear();
        
        $result = $package->install($primary);
        if (\is_string($result) == true) {
            $this->error($result);
            return false;
        }

        if ($primary == true) { 
            $package->setPrimary();
        }
        
        if (\is_array($result) == true) {
            $this->addErrors($result);
            return;
        }
    
        if ($runPostInstall == true) {
            // run post install actions
            $package->postInstall();
        }
                    
        $this->setResponse($result,function() use($name,$type) {
            $this
                ->message($type . '.update')
                ->field('type',$type)   
                ->field('name',$name);         
        },'errors.' . $type  . '.update');
    }

    /**
     * Enable/Disable package
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setStatus($request, $response, $data)
    { 
        $data->validate(true);

        $this->get('cache')->clear();

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
    }

    /**
     * Save module config
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function saveConfig($request, $response, $data)
    {
        $data->validate(true);   

        $this->get('cache')->clear();

        $module = Model::Modules()->FindByColumn('name',$data['name']);
        $module->config = $data->toArray();
        $result = $module->save();
        
        $this->setResponse($result,'module.config','errors.module.config');
    }

    /**
     * Set primary package
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setPrimary($request, $response, $data)
    {      
        $data->validate(true);    

        $this->get('cache')->clear();

        $name = $data['name'];
        $type = $data->get('type','template');

        $packageManager = $this->get('packages')->create($type);            
        
        $package = $packageManager->createPackage($name);
        $result = ($package != null) ? $package->setPrimary() : false;
        
        $this->setResponse($result,function() use($name,$type) {         
            $this
                ->message($type . '.primary')
                ->field('name',$name);         
        },'errors.' . $type . '.primary'); 
    }

    /**
     * Set ui library params
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setLibraryParams($request, $response, $data)
    {        
        $data->validate(true);    

        $name = $data['name'];
        $libraryParams = $data->get('params',[]);
        
        $packageManager = $this->get('packages')->create('library');
        $package = $packageManager->createPackage($name);

        $result = $package->saveLibraryParams($libraryParams);

        $this->setResponse($result,function() use($name) {                        
            $this
                ->message('library.params')
                ->field('name',$name);         
        },'errors.library.params'); 
    }

     /**
     * Set ui library status
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setLibraryStatus($request, $response, $data)
    {        
        $data->validate(true);    

        $name =  $data->get('name');
        $status = (bool)$data->get('status',false);

        $packageManager = $this->get('packages')->create('library');
        $library = $packageManager->createPackage($name);
        $library->setStatus($status);

        $result = $library->savePackageProperties();
        $this->get('cache')->clear();

        $this->setResponse($result,function() use($name,$status) {                        
            $this
                ->message('library.status')
                ->field('status',$status)
                ->field('name',$name);         
        },'errors.library.params'); 
    }
}
