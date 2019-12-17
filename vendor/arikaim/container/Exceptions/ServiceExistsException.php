<?php
/**
 *  Arikaim Container
 *  Dependency injection container component
 *  @link        http://www.arikaim.com
 *  @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license     MIT License
 */
namespace Arikaim\Container\Exceptions;

use Psr\Container\ContainerExceptionInterface;
use Exception;

/**
 * Service exists in container exception
 * 
 */
class ServiceExistsException extends \InvalidArgumentException implements ContainerExceptionInterface
{    
    /**
     * Constructor
     *
     * @param integer $id
     * @param integer $code
     * @param \Exception $previous
     */
    public function __construct($id, $code = 0, Exception $previous = null) {    
        parent::__construct("Service $id exists. Use replace function.", $code, $previous);
    }
}
