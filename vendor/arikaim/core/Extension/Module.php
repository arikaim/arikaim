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
use Closure;

/**
 * Module class.
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
     * test error
     *
     * @var string|null
     */
    protected $error = null;

    /**
     * Module name
     *
     * @var string
     */
    protected $moduleName = '';

    /**
     * Console command classes
     *
     * @var array
     */
    protected $consoleCommandClasses = [];

    /**
     * Middleware classes
     *
     * @var array
     */
    protected $middlewareClasses = [];

    /**
     * Call
     *
     * @param string $name
     * @param array $args
     * @return mxied
     */
    public function __call($name, $args)
    {      
        $closure = $this->{$name};

        if ($closure instanceof Closure) {
            return \call_user_func_array($closure->bindTo($this),$args);
        }
    }

    /**
     * Add console class
     *
     * @param string $class
     * @return void
     */
    public function addConsoleClass(string $class): void
    {
        $this->consoleCommandClasses[] = $class;
        $this->consoleCommandClasses = \array_unique($this->consoleCommandClasses);
    }

    /**
     * Get console commands classes
     *
     * @return array
     */
    public function getConsoleCommandClasses(): array
    {
        return $this->consoleCommandClasses;
    }

    /**
     * get middleware classes
     *
     * @return array
     */
    public function getMiddlewareClasses(): array
    {
        return $this->middlewareClasses;
    }

    /**
     * Add middleware class
     *
     * @param string $class
     * @return void
     */
    public function addMiddlewareClass(string $class): void
    {
        $this->middlewareClasses[] = $class;
        $this->middlewareClasses = \array_unique($this->middlewareClasses);
    }

    /**
     * Get module instance
     *
     * @return mixed|null
    */
    public function getInstance()
    {
        return null;
    }

    /**
     * Install module
     *
     * @return void
     */
    public function install()
    {     
    }

    /**
     * Get module name
     *
     * @return string
     */
    public function getModuleName(): string
    {        
        return $this->moduleName;
    }
    
    /**
     * Set module name
     *
     * @return void
     */
    public function setModuleName(string $name): void
    {        
        $this->moduleName = $name;
    }

    /**
     * Boot module
     *
     * @return void
     */
    public function boot()
    {        
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
     * @return string|null
     */
    public function getTestError(): ?string
    {
        return $this->error;
    }

    /**
     * Set module config
     * @param array $config
     * @return void
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
    
    /**
     * Get module config
     *
     * @param string|null $key
     * @return array
     */
    public function getConfig(?string $key = null): ?array
    {
        if (empty($key) == true) {
            return $this->config;
        }

        return $this->config[$key] ?? null;
    }
}
