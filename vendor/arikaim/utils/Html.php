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
 * Html builder
 */
class Html 
{
    /**
     * Html document content;
     *
     * @var string
     */
    private static $document;

    /**
     * Append or replace content
     *
     * @var boolean
     */
    private static $append;

    /**
     * Get html tag code
     *
     * @param string|null $content
     * @param string $name
     * @param array $attributes
     * @param boolean $singleTag
     * @param boolean $startTagOnly
     * @return string
     */
    public static function htmlTag($name, $content, $attributes = null, $singleTag = false, $startTagOnly = false)
    {    
        $attributes = Self::getAttributes($attributes);
        if ($singleTag == true) {
            return "<$name $attributes />";
        }
        if ($startTagOnly == true) {
            return "<$name $attributes>";
        }

        return "<$name $attributes>$content</$name>";   
    }

    /**
     * Call static methods
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $content = (isset($arguments[0]) == true) ? $arguments[0] : '';
        if (substr($name,0,5) == 'start') {
            $tag = strtolower(str_replace('start','',$name));
            $html = Self::startTag($tag,$content,$arguments);
        } elseif (substr($name,0,3) == 'end') {
            $tag = strtolower(str_replace('end','',$name));
            $html = Self::endTag($tag,$content);
        } else {
            $html = Self::htmlTag($name,$content,$arguments);
        }
        Self::appendHtml($html);

        return $html;
    }

    /**
     * Convert attributes array to string
     *
     * @param array $attributes
     * @return string
     */
    public static function getAttributes($attributes)
    {        
        if (is_array($attributes) == false) {
            return "";
        }
        $result = "";   
        foreach ($attributes as $key => $value) {
            if ($key == "content" || is_array($value) == true) continue;          
            $result .= " " . Self::attr($value,$key);
        }

        return $result;   
    }

    /**
     * Get html attribute
     *
     * @param string $value
     * @param string $name
     * @param string $default
     * @return string
     */
    public static function attr($value, $name = null, $default = null)
    {   
        $value = (empty($value) == true) ? $default : $value;

        return (empty($value) == false) ? "$name=\"$value\"" : "";
    }

    /**
     * Get html single tag
     *
     * @param string $name
     * @param string $attributes
     * @return string
     */
    public static function singleTag($name, $attributes = null)
    {        
        return Self::htmlTag($name,null,$attributes,true);
    }

    /**
     * Get html start tag
     *
     * @param string $name
     * @param string $attributes
     * @return string
     */
    public static function startTag($name, $attributes = null)
    {        
        return Self::htmlTag($name,null,$attributes,false,true);
    }

    /**
     * Get html end tag
     *
     * @param string $name
     * @param string $content
     * @return string
     */
    public static function endTag($name, $content = '')
    {        
        return "$content</$name>";
    }

    /**
     * Decode html chars 
     *
     * @param string $value
     * @return string
     */
    public static function specialcharsDecode($value)
    {
        return htmlspecialchars_decode($value,ENT_HTML5 | ENT_QUOTES);
    }

    /**
     * Remove html gams from text
     *
     * @param string $text
     * @param string|array $tags
     * @return string
     */
    public static function removeTags($text, $tags)
    {
        if (is_string($tags) == true) {
            $tags = [$tags];
        }
        foreach ($tags as $tag) {
            $replace = preg_replace("#\\<" . $tag . "(.*)/" . $tag . ">#iUs","", $text);
            $text = ($replace !== null) ? $replace : $text;  
        }

        return $text;
    }

    /**
     * Start html
     *
     * @return void
     */
    public static function startDocument()
    {
        Self::$document = '';
        Self::$append = true;
    }

    /**
     * Show html code
     *
     * @return string
     */
    public static function renderDocument()
    {
        echo Self::$document; 
    }

    /**
     * Get html code
     *
     * @return string
     */
    public static function getDocument()
    {
        return Self::$document; 
    }
    
    /**
     * Append html code
     *
     * @param string $html
     * @return void
     */
    public static function appendHtml($html)
    {
        if (Self::$append == true) {
            Self::$document .= $html;
        }
    }
}
