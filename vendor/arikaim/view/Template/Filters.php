<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\View\Template;

use Arikaim\Core\Utils\Html;
use Arikaim\Core\Collection\Arrays;

/**
 * Template filer functions
 */
class Filters  
{
    /**
     * Check if $value = $var2 
     *
     * @param mixed $value
     * @param mixed $var2
     * @param mixed $returnValue
     * @return mixed|null
     */
    public static function is($value, $var2, $returnValue)
    {
        if (is_array($value) == true) {
            if (in_array($value,$var2) == true) {
                return $returnValue;
            }
        }
        if ($value === 'false') $value = false;
        if ($value === 'true')  $value = true;

        return ($value == $var2) ? $returnValue : null;                
    }   

    /**
     * Dump var
     *
     * @param mixed $value
     * @return void
     */
    public static function dump($value)
    {
        return (is_array($value) == true) ? print_r($value) : var_dump($value);
    }

    /**
     * Return label if value is empty
     *
     * @param mixed $value
     * @param string $label
     * @return mxied
     */
    public static function emptyLabel($value, $label)
    {
        return (empty($value) == true) ? $label : $value;
    }

    /**
     * Slice text and add label 
     *
     * @param string $text
     * @param integer $size
     * @param string $label
     * @return string
     */
    public static function sliceLabel($text, $size, $label = '...')
    {
        return (strlen($text) > $size) ? substr($text,0,$size) . $label : $text;          
    }

    /**
     * Convert value to string
     *
     * @param mixed $value
     * @param string $separator
     * @return string
     */
    public static function convertToString($value, $separator = " ")
    {
        if (is_bool($value) === true) {
            return ($value === true) ? 'true' : 'false';
        }  
        if (is_array($value) === true) {    
            return Arrays::toString($value,$separator);
        }
        
        return (string)$value;
    }

    /**
     * Convert value to html attribute(s)
     *
     * @param mixed|array $value
     * @param string|null $name
     * @param mixed $default
     * @return void
     */
    public static function attr($value, $name = null, $default = null)
    {      
        return (is_array($value) == true) ? Html::getAttributes($value) : Html::attr($value,$name,$default);
    }
}
