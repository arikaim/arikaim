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
    const DEFAULT_FORMAT_NAME = 'default';
    const ACCOUNTING_FORMAT_NAME = 'accounting';

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
        'decimals_separator'  => '.',
        'thousands_separator' => ','
    ]; 

    /**
     * Default format values
     *   
     */
    const ACCOUNTING_FORMAT = [      
        'decimals'            => 3,
        'decimals_separator'  => '.',
        'thousands_separator' => ','
    ]; 

    /**
     *  Formats list
     */
    const FORMATS_LIST = [
        'default'    => Self::DEFAULT_FORMAT,
        'accounting' => Self::ACCOUNTING_FORMAT
    ];

    /**
     *  Text values which may convert to boolean
    */
    const BOOLEAN_TEXT_VALUES = ['true','false','0','1','on','off','yes','no'];

    /**
     * Number format
     *
     * @var array|null
     */
    private static $format = null;

    /**
     * Format number
     *
     * @param integer|float $number
     * @param string|null|array $format
     * @return integer|float
     */
    public static function format($number, $format = null)
    {
        $number = $number ?? 0;
        $format = Self::resolveFormat($format);

        return \number_format($number,$format['decimals'],$format['decimals_separator'],$format['thousands_separator']);
    }

    /**
     * Resolve format
     *
     * @param string|array|null $format
     * @return array
     */
    public static function resolveFormat($format)
    {
        if (\is_array($format) == true) {
            return Self::resolveFormatArray($format);
        }

        if (empty($format) == true) {
            $formatName = \constant('CURRENT_NUMBER_FORMAT') ?? 'default';
            return (isset(Self::FORMATS_LIST[$formatName]) == true) ? Self::FORMATS_LIST[$formatName] : Self::DEFAULT_FORMAT;
        }

        $tokens = \explode(',',$format);     
    
        switch (\trim($tokens[0])) {
            case Self::DEFAULT_FORMAT_NAME:
                return Self::DEFAULT_FORMAT;   
            case Self::ACCOUNTING_FORMAT_NAME:
                return Self::resolveFormatArray([3,'.',',']);       
        }
       
        return Self::resolveFormatArray($tokens);       
    }

    /**
     * Resolbe format array
     *
     * @param array $format
     * @return array
     */
    private static function resolveFormatArray(array $format): array
    {
        return [
            'decimals'            => empty($format['decimals']) ? $format[0] ?? 2 : $format['decimals'],
            'decimals_separator'  => empty($format['decimals_separator']) ? $format[1] ?? '.' : $format['decimals_separator'],
            'thousands_separator' => empty($format['thousands_separator']) ? $format[2] ?? ',' : $format['thousands_separator']
        ];
    }

    /**
     * Set number format
     *
     * @param mixed $format
     * @return void
     */
    public static function setFormat($format): void
    {      
        Self::$format = Self::resolveFormat($format);
    }

    /**
     * Get format options
     *
     * @return array
     */
    public static function getFormat(): array
    {              
        return (Self::$format === null) ? Self::DEFAULT_FORMAT : Self::$format;                
    }

    /**
     * Return true if variable is number
     *
     * @param mixed $variable
     * @return boolean
     */
    public static function isNumber($variable): bool
    {
        return \is_numeric($variable);
    }

    /**
     * Return true if variable is float
     *
     * @param mixed $variable
     * @return boolean
     */
    public static function isFloat($variable): bool
    {
        return \is_float($variable);
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
    public static function getInteger($value): int
    {
        return \intval($value);
    }

    /**
     * Get number fraction
     *
     * @param mixed $value
     * @return float
     */
    public static function getFraction($value)
    {
        return ($value - \intval($value));
    }

    /**
     * Return true if text is boolean value
     *
     * @param string $text
     * @return boolean
     */
    public static function isBoolean($text): bool
    {       
        $result = \filter_var($text,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);

        return !($result === null);
    }

    /**
     *  
     *  Sanitize number 
     * 
     *  @param mixed $number
     *  @return float
     */
    public static function sanitizeNumber($number, int $decimals = 2): float
    { 
        return (float)preg_replace("/[^0-9.]+/",'',$number);
    }

    /**
     * Convert text to bool value
     *
     * @param string $value
     * @return bool
     */
    public static function toBoolean($text): bool
    {
        $result = \filter_var($text,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);

        return ($result === null) ? false : (bool)$result;
    }

    /**
     * Type cast to int, foat, bool
     *
     * @param mixed $number
     * @return mixed
     */
    public static function toNumber($number)
    {
        if (\is_integer($number) == true) {
            return (int)$number;
        }
        if (\is_float($number) == true) {
            return (float)$number;
        }
        if (\is_numeric($number) == true) {
            return (float)$number;
        }

        if (Self::isBoolean($number) == true) {
            return Self::toBoolean($number);
        }

        return (string)$number;
    }
}
