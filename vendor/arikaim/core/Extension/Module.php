<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Extension;

use Arikaim\Core\Interfaces\ModuleInterface;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Arikaim;

/**
 * Base class for Arikaim modules.
 */
class Module implements ModuleInterface
{
    /**
     * Module config
     *
     * @var array
     */
    protected $config = [];

    /**
     * Service container item name
     *
     * @var string|null
     */
    protected $serviceName;
    
    /**
     * test error
     *
     * @var string|null
     */
    protected $error = null;

    /**
     * Install module
     *
     * @return bool
     */
    public function install()
    {
        return true;        
    }

    /**
      * Install driver
      *
      * @param string|object $name Driver name, full class name or driver object ref
      * @param string|null $class
      * @param string|null $category
      * @param string|null $title
      * @param string|null $description
      * @param string|null $version
      * @param array $config
      * @return boolean|Model
    */
    public function installDriver($name, $class = null, $category = null, $title = null, $description = null, $version = null, $config = [])
    {
        return Arikaim::driver()->install($name,$class,$category,$title,$description,$version,$config);
    }

    /**
     * Boot module
     *
     * @return bool
     */
    public function boot()
    {        
        return true;
    }
    
    /**
     * Get service container item name
     *
     * @return string|null
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Set service container item name
     *
     * @param string $name
     * @return void
     */
    public function setServiceName($name)
    {
        return $this->serviceName = $name;
    }

    /**
     * Test module function
     * 
     * @return bool
     */
    public function test()
    {        
        return true;
    }

    /**
     * Get test error
     *
     * @return string
     */
    public function getTestError()
    {
        return $this->error;
    }

    /**
     * Set module config
     * @param array $config
     * @return void
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }
    
    /**
     * Get module config
     *
     * @param string|null $key
     * @return array
     */
    public function getConfig($key = null)
    {
        if (empty($key) == true) {
            return $this->config;
        }

        return (isset($this->config[$key]) == true) ? $this->config[$key] : null;
    }

    /**
     * Load module config
     *
     * @param string $name
     * @return bool
     */
    protected function loadConfig($name)
    {
        $model = Model::Modules()->findByColumn($name,'name');
        if (is_object($model) == true) {
            $this->setConfig($model->config);
            return true;
        } 
        
        return false;
    }
}
