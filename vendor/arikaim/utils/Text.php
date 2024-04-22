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
     * Replace umlauts chars
     *
     * @param string $text
     * @return string
     */
    public static function replaceChars(string $text): string
    {
        $chars = [
            "ä" => "ae",
            "ö" => "oe",
            "ß" => "ss",
            "ü" => "ue",
            "æ" => "ae",
            "ø" => "oe",
            "å" => "aa",
            "é" => "e",
            "è" => "e",
            "ó" => "o",
            "ż" => "z",
            "ę" => "e",
            "ą" => "a",
            "ś" => "s",
            "Ł" => "l",
            "ź" => "z",
            "ć" => "c",
            "ń" => "n"
        ];

        return \str_replace(\array_keys($chars),\array_values($chars),$text);
    }

    /**
     * Clean Text
     *
     * @param string $text
     * @return string
     */
    public static function cleanText(?string $text): string
    {
        return \preg_replace('/[[:^print:]]/','',$text);
    }

    /**
     * Pad a string to a certain length with another string (both side)
     *
     * @param string $input
     * @param integer $length
     * @param string $char
     * @param boolean $htmlSafe
     * @return string
     */
    public static function pad(string $input, int $length, string $char = ' ', bool $htmlSafe = true): string
    {
        $output = \str_pad($input,$length,$char,STR_PAD_BOTH);

        return ($htmlSafe == true) ? \str_replace(' ','&nbsp;',$output) : $output;
    }

    /**
     * Pad left a string to a certain length with another string
     *
     * @param string $input
     * @param integer $length
     * @param string $char
     * @param boolean $htmlSafe
     * @return string
     */
    public static function padLeft(string $input, int $length, string $char = ' ', bool $htmlSafe = true): string
    {
        $output = \str_pad($input,$length,$char,STR_PAD_LEFT);

        return ($htmlSafe == true) ? \str_replace(' ','&nbsp;',$output) : $output;
    }

    /**
     * Pad right a string to a certain length with another string
     *
     * @param string $input
     * @param integer $length
     * @param string $char
     * @param boolean $htmlSafe
     * @return string
     */
    public static function padRight(string $input, int $length, string $char = ' ', bool $htmlSafe = true): string
    {
        $output = \str_pad($input,$length,$char, STR_PAD_RIGHT);

        return ($htmlSafe == true) ? \str_replace(' ','&nbsp;',$output) : $output;
    }

    /**
     * Mask text
     *
     * @param string $text
     * @param integer $len
     * @param string $maskChar
     * @return string
     */
    public static function mask(string $text, int $len = 5, string $maskChar = '*'): string
    {
        return \str_repeat($maskChar,\strlen($text) - $len) . \substr($text, - $len);           
    }

    /**
     * Upper case first letter for Utf8
     *
     * @param string $text
     * @return string
     */
    public static function ucFirstUtf(string $text): string
    {      
        return (\function_exists('mb_convert_case') == true ) ? \mb_convert_case($text,MB_CASE_TITLE,'UTF-8') : $text;         
    }

    /**
     * Slice text
     *
     * @param string $text
     * @param integer $maxLength
     * @return string
     */
    public static function sliceText(string $text, int $maxLength = 30): string
    {
        if (\strlen($text) > $maxLength) {
            $text = \substr(\trim($text),0,$maxLength);    
            $pos = \strrpos($text,' ');
            return ($pos > 0) ? \substr($text,0,$pos) : $text;   
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
    public static function tokenize($text, ...$options): array
    {
        $delimiter = $options[0] ?? ' ';
        $case = $options[1] ?? null;
        $unique = $options[2] ?? true;
        $removeChars = $options[3] ?? false;

        if (\is_string($text) == true) {
            $text = (empty($text) == true) ? [] : \explode($delimiter,$text); 
        } 
        $tokens = (\is_array($text) == true) ? $text : []; 
    
        if ($unique == true) {
            $tokens = \array_unique($tokens);
        }
     
        foreach ($tokens as $key => $value) {
            if (empty($value) == true) {
                continue;
            }
            $word = Self::transformWord($value,$case,$removeChars,$removeChars);

            if (empty($word) == true) {
                unset($tokens[$key]);
            } else {
                $tokens[$key] = $word;
            }
        }
         
        return $tokens;
    }

    /**
     * Transform word ( removes all not a-z chars )
     *
     * @param string $word
     * @param mixed  ...$options   1 - case
     * @return string
     */
    public static function transformWord(string $word, ...$options): string
    {       
        $case = $options[0] ?? Text::LOWER_CASE;
        $removeNumbers = $options[1] ?? false;
        $removeChars = $options[2] ?? false;

        if ($removeChars == true) {
            $word = Self::removeSpecialChars($word,$removeNumbers);
        }
       
        switch($case) {
            case Text::LOWER_CASE: 
                $word = \mb_strtolower($word);
                break;
            case Text::UPPER_CASE: 
                $word = \mb_strtoupper($word);
                break;
            case Text::FIRST_LETTER_UPPER:
                $word = \ucfirst($word);
                break;
        }

        return \trim($word);
    }

    /**
     * Remove special chars and numbers from text
     *
     * @param string $text
     * @param boolean $removeNumbers
     * @return string
     */
    public static function removeSpecialChars(string $text, bool $removeNumbers = false): string 
    {        
        return ($removeNumbers == true) ? \preg_replace('/[^a-zA-Z ]/i','',\trim($text)) : \preg_replace('/[^a-zA-Z0-9]/','',$text);
    }

    /**
     * Convert to title (pascal) case
     *
     * @param string $text
     * @return string
     */
    public static function convertToTitleCase(string $text): string
    {      
        $tokens = \explode('_',$text);
        $result = '';
        foreach ($tokens as $word) {
            $result .= \ucfirst($word);
        }

        return $result;
    }

    /**
     * Replace all code {{ var }} in text with var value
     * 
     * @param string|null $text
     * @param array $vars
     * @return string
     */
    public static function render(?string $text, array $vars = []): string 
    {    
        $text = $text ?? '';
        $result = \preg_replace_callback('/\{\{(.*?)\}\}/',function ($matches) use ($vars) {
            $variableName = \trim($matches[1]);
            return (\array_key_exists($variableName,$vars) == true) ? $vars[$variableName] : '';               
        },$text);
       
        return ($result === null) ? $text : $result;        
    }

    /**
     * Render multiple text items
     *
     * @param array $items
     * @param array $vars
     * @return array
     */
    public static function renderMultiple(array $items, array $vars = []): array
    {
        foreach ($items as $key => $value) {           
            if ((\is_string($value) == true) || (empty($value) == true)) {
                $items[$key] = Text::render($value,$vars);
            }
        }
        
        return $items;
    }

    /**
     * Ceate random token
     *
     * @param int $length
     * @return string
     */
    public static function createToken(int $length = 22): string
    {
        $token = '';
        while (($len = \strlen($token)) < $length) {
            $size = $length - $len;
            $bytes = \random_bytes($size);
            $token .= \substr(\str_replace(['/','+','='],'',\base64_encode($bytes)),0,$size);
        }
        
        return $token;
    }

    /**
     * Create random text 
     *
     * @param integer $length
     * @param string|null $keyspace
     * @return string
     */
    public static function random(int $length = 10, ?string $keyspace = null): string 
    {
        if (empty($keyspace) == true) {
            $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
    
        return \substr(\str_shuffle($keyspace),0,$length);
    }

    /**
     * Create randowm string
     *
     * @param integer $length
     * @return string
     */
    public static function randomString(int $length = 10): string
    {
        return \base64_encode(\hex2bin(\substr(\uniqid(),- $length)));
    }
}
