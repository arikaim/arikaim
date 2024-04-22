<?php
/**
 *  Arikaim Container
 *  Dependency injection container component
 *  @link        http://www.arikaim.com
 *  @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license     MIT License
 */
namespace Arikaim\Container;

/**
 * Arikaim container interface 
 */
interface ArikaimContainerInterface 
{  
   /**
     * Call service method
     *
     * @param string $id
     * @param string $method
     * @param array $params
     * @return mixed|null
     */
    public function call($id, $method, array $params = []);
}
