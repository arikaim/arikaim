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
 * text helpers
 */
class Text 
{
    const LOWER_CASE         = 1;
    const UPPER_CASE         = 2;
    const FIRST_LETTER_UPPER = 3;

    /**
     * Slice text
     *
     * @param string $text
     * @param integer $maxLength
     * @return string
     */
    public static function sliceText($text, $maxLength)
    {
        if (strlen($text) > $maxLength) {
            $text = substr(trim($text),0,$maxLength);    
            $pos = strrpos($text,' ');
            return ($pos > 0) ? substr($text,0,$pos) : $text;   
        }
        
        return $text;
    }

    /**
     * Tokenize text split to words
     *
     * @param string|array $text
     * @param mixed ...$options
     * @return array
     */
    public static function tokenize($text, ...$options)
    {
        $delimiter = (isset($options[0]) == true) ? $options[0] : ' ';
        $case = (isset($options[1]) == true) ? $options[1] : null;
        $unique = (isset($options[2]) == true) ? $options[2] : true;

        $tokens = (is_string($text) == true) ? explode($delimiter,$text) : $text; 
    
        if ($unique == true) {
            $tokens = array_unique($tokens);
        }

        foreach ($tokens as $key => $value) {
            if (empty($value) == true) {
                continue;
            }
            $word = Self::transformWord($value,$case);
            if (empty($word) == true) {
                unset($tokens[$key]);
            } else {
                $tokens[$key] = $word;
            }
        }
         
        return $tokens;
    }

    /**
     * Transfor word ( removes all not a-z chars )
     *
     * @param string $word
     * @param mixed  ...$options   1 - case
     * @return void
     */
    public static function transformWord($word, ...$options)
    {       
        $case = (isset($options[0]) == true) ? $options[0] : Text::LOWER_CASE;
        $removeNumbers = (isset($options[1]) == true) ? $options[1] : false;

        $word = Self::removeSpecialChars($word,$removeNumbers);

        switch($case) {
            case Text::LOWER_CASE: 
                $word = \strtolower($word);
                break;
            case Text::UPPER_CASE: 
                $word = \strtoupper($word);
                break;
            case Text::FIRST_LETTER_UPPER:
                $word = \ucfirst($word);
                break;
        }

        return trim($word);
    }

    /**
     * Remove special chars and numbers from text
     *
     * @param string $text
     * @param boolean $removeNumbers
     * @return string
     */
    public static function removeSpecialChars($text, $removeNumbers = false) 
    {        
        return ($removeNumbers == true) ? preg_replace('/[^a-zA-Z ]/i','',trim($text)) : preg_replace("/[^a-zA-Z0-9]/","",$text);
    }

    /**
     * Convert to title (pascal) case
     *
     * @param string $text
     * @return string
     */
    public static function convertToTitleCase($text)
    {      
        $tokens = explode('_',$text);
        $result = '';
        foreach ($tokens as $word) {
            $result .= \ucfirst($word);
        }

        return $result;
    }

    /**
     * Replace all code {{ var }} in text with var value
     * 
     * @param string $text
     * @param array $vars
     * @return string
     */
    public static function render($text, $vars = []) 
    {    
        $result = preg_replace_callback("/\{\{(.*?)\}\}/", function ($matches) use ($vars) {
            $variableName = trim(strtolower($matches[1]));
            if ( array_key_exists($variableName,$vars) == true ) {
                return $vars[$variableName];
            }
            return "";
        },$text);

        return ($result == null) ? $text : $result;        
    }

    /**
     * Render multiple text items
     *
     * @param array $items
     * @param array $vars
     * @return array
     */
    public static function renderMultiple(array $items, $vars = [])
    {
        foreach ($items as $key => $value) {          
            if (is_string($value) == true) {
                $items[$key] = Text::render($value,$vars);
            }
        }
        
        return $items;
    }

    /**
     * Ceate random token
     *
     * @param  int $length
     * @return string
     */
    public static function createToken($length = 22)
    {
        $token = '';
        while (($len = strlen($token)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $token .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        
        return $token;
    }
}
