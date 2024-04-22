<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces;

/**
 * Module interface
 */
interface ModuleInterface
{   
    /**
     * Get module instance
     *
     * @return mixed|null
     */
    public function getInstance();

    /**
     * Get module name
     *
     * @return string
     */
    public function getModuleName(): string;

    /**
     * Set module name
     *
     * @return void
     */
    public function setModuleName(string $name): void;

    /**
     * Boot module callback
     *
     * @return void
     */
    public function boot();

    /**
     * Install module callbaxk
     *
     * @return void
     */
    public function install();

    /**
     * Test module
     *
     * @return bool
     */
    public function test();

    /**
     * Get etst error
     *
     * @return string|null
     */
    public function getTestError(): ?string;

    /**
     * Set module config
     * 
     * @param array $config 
     * @return void
     */
    public function setConfig(array $config): void;
    
    /**
     * Get module config
     * 
     * @param string|null $key
     * @return array
     */
    public function getConfig(?string $key = null): ?array;
}
