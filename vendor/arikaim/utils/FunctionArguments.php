<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Utils;

/**
 * Function arguments helper
 */
class FunctionArguments 
{   
    const BOOLEAN_TYPE = 'boolean';
    const INTEGER_TYPE = 'integer';
    const DOUBLE_TYPE  = 'double';
    const STRING_TYPE  = 'string';
    const ARRAY_TYPE   = 'array';
    const OBJECT_TYPE  = 'object';
    const NULL_TYPE    = 'NULL';
    const UNKNOWN_TYPE = 'unknown type';
    
    /**
     * Get funciton argument
     *
     * @param array $args
     * @param integer $index
     * @param mixed $type
     * @return mixed|null
     */
    public static function getArgument(array $args, int $index, $type = null)
    {       
        if (isset($args[$index]) == false) {
            return null;
        }
        $variableType = \gettype($args[$index]);

        return ($type != null && $variableType != $type) ? null : $args[$index];  
    }
}
