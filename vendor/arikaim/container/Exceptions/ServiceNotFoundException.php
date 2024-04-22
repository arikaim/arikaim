<?php
/**
 *  Arikaim Container
 *  Dependency injection container component
 *  @link        http://www.arikaim.com
 *  @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license     MIT License
 */
namespace Arikaim\Container\Exceptions;

use Psr\Container\NotFoundExceptionInterface;
use Exception;

/**
 * Service not found in container exception
 * 
 */
class ServiceNotFoundException extends \InvalidArgumentException implements NotFoundExceptionInterface
{  
    /**
     * Constructor
     *
     * @param integer $id
     * @param integer $code
     * @param Exception $previous
     */  
    public function __construct($id, $code = 0, Exception $previous = null) {    
        parent::__construct('Service ' . $id . ' not exists.',$code,$previous);
    }
}
