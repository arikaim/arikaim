<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     View
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
        if (\is_array($value) == true) {
            if (\in_array($value,$var2) == true) {
                return $returnValue;
            }
        }
        $value = (\is_bool($value) == true) ? (int)$value : $value;
        $value = ($value === 'false') ? false : $value;
        $value = ($value === 'true') ? true : $value;
      
        return ($value == $var2) ? $returnValue : null;                
    }   

    /**
     * Dump var
     *
     * @param mixed $value
     * @return mixed
     */
    public static function dump($value)
    {
        return (\is_array($value) == true) ? \print_r($value) : \var_dump($value);
    }

    /**
     * Return label if value is empty
     *
     * @param mixed $value
     * @param string|null $label
     * @return mixed
     */
    public static function emptyLabel($value, ?string $label = '')
    {
        return (empty($value) == true) ? $label ?? '' : $value;
    }

    /**
     * Slice text and add label 
     *
     * @param string $text
     * @param integer $size
     * @param string|null $label
     * @return string
     */
    public static function sliceLabel(?string $text, int $size = 30, ?string $label = '...'): string
    {
        $text = $text ?? '';

        return (\strlen($text) > $size) ? \mb_substr($text,0,$size) . $label : $text;          
    }

    /**
     * Convert value to string
     *
     * @param mixed $value
     * @param string|null $separator
     * @return string
     */
    public static function convertToString($value, ?string $separator = ' '): string
    {
        if (\is_bool($value) === true) {
            return ($value === true) ? 'true' : 'false';
        }  

        return (\is_array($value) === true) ? Arrays::toString($value,$separator) : (string)$value;            
    }

    /**
     * Convert value to html attribute(s)
     *
     * @param mixed|array $value
     * @param string|null $name
     * @param mixed $default
     * @return string
     */
    public static function attr($value, ?string $name = null, $default = '')
    {      
        return (\is_array($value) == true) ? Html::getAttributes($value) : Html::attr($value,$name ?? '',$default ?? '');
    }
}
