<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Validator\Filter;

use Arikaim\Core\Validator\Filter;

/**
 * Remove html tags filter
 */
class StripTags extends Filter
{  
    /**
     * Filter value, return filtered value
     *
     * @param mixed $value
     * @return mixed
     */
    public function filterValue($value) 
    {          
        if (\is_string($value) == true) {
            return \strip_tags($value,$this->params ?? []);     
        }  
        if (\is_array($value) == true) {
            foreach ($value as $key => $item) {
                $value[$key] = \strip_tags($item,$this->params ?? []);
            }
        }
          
        return $value;
    } 

    /**
     * Return filter type
     *
     * @return mixed
     */
    public function getType()
    {       
        return FILTER_CALLBACK;
    }
}
