<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */

/**
 * Create event obj
 *
 * @param array $params Event params
 * @return Arikaim\Core\Events\Event 
 */
function createEvent($params = [])
{
    return new Arikaim\Core\Events\Event($params);
}

/**
 * Return default value if variable is null or empty.
 *
 * @param mixed $variable
 * @param mixed $default
 * @return mixed
 */
function defaultValue($variable, $default)
{
    return (empty($variable) == true) ? $default : $variable; 
}

/**
 * Call closure
 *
 * @param mixed $value
 * @param \Closure $closure
 * @return mixed
 */
function call($value, $closure)
{
    $closure($value);
    
    return $value;
}
