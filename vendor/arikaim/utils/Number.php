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
 * Number helper
 */
class Number 
{   
    /**
     * Number formats
     *
     * @var array
     */
    protected static $formats = [];

    /**
     * Default format values
     *   
     */
    const DEFAULT_FORMAT = [      
        'decimals'            => 2,
        'decimals_separator'  => ".",
        'thousands_separator' => ","
    ]; 

    /**
     * Number format
     *
     * @var array
     */
    private static $format;

    /**
     * Format number
     *
     * @param integer|float $number
     * @param string|null|array $formatName
     * @return integer|float
     */
    public static function format($number, $formatName = null)
    {
        $format = Self::getFormat($formatName);

        return number_format($number,$format['decimals'],$format['decimals_separator'],$format['thousands_separator']);
    }

    /**
     * Resolve format
     *
     * @param string|array|null $format
     * @return array
     */
    public static function resolveFormat($format)
    {
        if (is_array($format) == true) {
            return [
                'decimals'            => (isset($format[0]) == true) ? $format[0] : 2,
                'decimals_separator'  => (isset($format[1]) == true) ? $format[1] : ".",
                'thousands_separator' => (isset($format[2]) == true) ? $format[2] : ","
            ];
        }

        return Self::getFormat($format);       
    }

    /**
     * Set formats list
     *
     * @param array $items
     * @param array|null $default
     * @return void
     */
    public static function setFormats(array $items, $default = null)
    {
        Self::$formats = $items;

        if (empty($default) == false) {
            Self::setFormat($default);
        }
    }

    /**
     * Set number format
     *
     * @param mixed $format
     * @return void
     */
    public static function setFormat($format)
    {      
        Self::$format = Self::resolveFormat($format);
    }

    /**
     * Get format options
     *
     * @param string $name
     * @return array
     */
    public static function getFormat($name = null)
    {
        if (empty($name) == true) {
            return (empty(Self::$format) == true) ? Self::DEFAULT_FORMAT : Self::$format;
        } 

        return (isset(Self::$formats[$name]) == true) ? Self::$formats[$name] : Self::$defaultFormat;          
    }

    /**
     * Return true if variable is number
     *
     * @param mixed $variable
     * @return boolean
     */
    public static function isNumber($variable)
    {
        return is_numeric($variable);
    }

    /**
     * Return true if variable is float
     *
     * @param mixed $variable
     * @return boolean
     */
    public static function isFloat($variable)
    {
        return is_float($variable);
    }

    /**
     * Return 0 if variable is not number
     *
     * @param mixed $value
     * @return integer|float
     */
    public static function getNumericValue($value) 
    {
        return (Self::isNumber($value) == false) ? 0 : $value;
    }

    /**
     * Get integer value
     *
     * @param mixed $value
     * @return integer
     */
    public static function getInteger($value)
    {
        return intval($value);
    }

    /**
     * Get number fraction
     *
     * @param mixed $value
     * @return float
     */
    public static function getFraction($value)
    {
        return ($value - intval($value));
    }
}
