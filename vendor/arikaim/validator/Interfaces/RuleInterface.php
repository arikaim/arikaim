<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Validator\Interfaces;

/**
 * Validation rule interface
 */
interface RuleInterface
{    
    /**
     * Get rule type 
     *
     * @return mixed
     */
    public function getType(); 

    /**
     * Executed if rule type is FILTER_CALLBACK
     *
     * @param mixed $value
     * @return bool
     */
    public function validate($value): bool; 

    /**
     * Return rule params
     *
     * @return Collection
     */
    public function getParams();

    /**
     * Set validation error ode
     *
     * @param string|null $error
     * @param string|null $default
     * @return void
    */
    public function setError(?string $error, ?string $default = null): void;

    /**
     * Set default error code
     *
     * @param string $errorCode
     * @return void
     */
    public function setDefaultError(string $errorCode): void;
}
