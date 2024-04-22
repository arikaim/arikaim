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

use Arikaim\Core\Collection\Properties;

/**
 * Properties config interface
 */
interface ConfigPropertiesInterface
{   
    /**
     * Create config properties array
     *  
     * @param array|null $values 
     * @return array
    */
    public function createConfigProperties(?array $values = null): array;

    /**
     * Get config properties collection
     *
     * @param array|null $config
     * @return Properties
     */
    public function getConfigProperties(?array $config = null): Properties;

    /**
     * Init config properties
     *
     * @param Properties $properties
     * @return void
     */
    public function initConfigProperties(Properties $properties): void;

    /**
     * Get config properties collection
     *
     * @param Properties|array $properties
     * @return void
    */
    public function setConfigProperties($properties): void;
}
