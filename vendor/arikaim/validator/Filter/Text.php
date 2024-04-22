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
 * Text filter
 */
class Text extends Filter
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
            return \filter_var(\trim($value),FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
        }
        
        if (\is_array($value) == true) {
            foreach ($value as $key => $item) {
                $value[$key] = \filter_var(\trim($item ?? ''),FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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
