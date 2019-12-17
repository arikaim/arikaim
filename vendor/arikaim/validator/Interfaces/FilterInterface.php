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
 * Variable filter interface
 */
interface FilterInterface
{    
    /**
     * Get filter type 
     *
     * @return integer
     */
    public function getType(); 

    /**
     * Executed if filter type is FILTER_CALLBACK
     *
     * @param mixed $value
     * @return mixed
     */
    public function filterValue($value); 
}
