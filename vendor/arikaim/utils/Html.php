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
    private static $document = '';

    /**
     * Append or replace content
     *
     * @var boolean
     */
    private static $append;

    /**
     * Create valid html element id
     *
     * @param string $text
     * @param string $separator
     * @return string
     */
    public static function createId(string $text, string $separator = '-'): string
    {
        $text = \trim($text);
        $text = \preg_replace('/\s+/',$separator,$text);
        $text = \str_replace(DIRECTORY_SEPARATOR,$separator,$text);
        $text = \str_replace('/',$separator,$text);

        return \strtolower($text);
    }

    /**
     * Get html tag code
     *    
     * @param string $name
     * @param string|null $content
     * @param array|null $attributes
     * @param boolean $singleTag
     * @param boolean $startTagOnly
     * @return string
     */
    public static function htmlTag(
        string $name, 
        ?string $content, 
        ?array $attributes = null, 
        bool $singleTag = false, 
        bool $startTagOnly = false
    ): string
    {    
        $attributes = Self::getAttributes($attributes);
        if ($singleTag == true) {
            return '<' . $name . ' ' . $attributes . "/>";
        }
        if ($startTagOnly == true) {
            return '<' . $name . ' ' . $attributes . '>';
        }

        return '<' . $name . ' ' . $attributes . ' >' . $content . "</" . $name .'>';   
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
        if (\substr($name,0,5) == 'start') {
            $tag = \strtolower(\str_replace('start','',$name));
            $html = Self::startTag($tag,$content,$arguments);
        } elseif (\substr($name,0,3) == 'end') {
            $tag = \strtolower(\str_replace('end','',$name));
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
     * @param array|null $attributes
     * @return string
     */
    public static function getAttributes(?array $attributes): string
    {        
        if (\is_array($attributes) == false) {
            return '';
        }
        $result = '';   
        foreach ($attributes as $key => $value) {
            if ($key == 'content' || \is_array($value) == true) continue;          
            $result .= ' ' . Self::attr($value,$key);
        }

        return $result;   
    }

    /**
     * Get html attribute
     *
     * @param string|null $value
     * @param string|null $name
     * @param string|null $default
     * @return string
     */
    public static function attr(?string $value, ?string $name = null, ?string $default = null): string
    {   
        $value = (empty($value) == true) ? $default : $value;

        return (empty($value) == false) ? $name . "=\"" . $value . "\"" : '';
    }

    /**
     * Get html single tag
     *
     * @param string $name
     * @param array|null $attributes
     * @return string
     */
    public static function singleTag(string $name, ?array $attributes = null): string
    {        
        return Self::htmlTag($name,null,$attributes,true);
    }

    /**
     * Get html start tag
     *
     * @param string $name
     * @param array|null $attributes
     * @return string
     */
    public static function startTag(string $name, ?array $attributes = null)
    {        
        return Self::htmlTag($name,null,$attributes,false,true);
    }

    /**
     * Get html end tag
     *
     * @param string $name
     * @param string|null $content
     * @return string
     */
    public static function endTag(string $name, ?string $content = ''): string
    {        
        return $content . "</" . $name . '>';
    }

    /**
     * Decode html chars 
     *
     * @param string $value
     * @return string
     */
    public static function specialcharsDecode($value)
    {
        return \htmlspecialchars_decode($value,ENT_HTML5 | ENT_QUOTES);
    }

    /**
     * Remove html gams from text
     *
     * @param string $text
     * @param string|array $tags
     * @return string|null
     */
    public static function removeTags(string $text, $tags): ?string
    {
        $tags = (\is_string($tags) == true) ? [$tags] : $tags;           
        foreach ($tags as $tag) {
            $replace = \preg_replace("#\\<" . $tag . "(.*)/" . $tag . '>#iUs','',$text);
            $text = ($replace !== null) ? $replace : $text;  
        }

        return $text;
    }

    /**
     * Start html
     *
     * @return void
     */
    public static function startDocument(): void
    {
        Self::$document = '';
        Self::$append = true;
    }

    /**
     * Show html code
     *
     * @return string
     */
    public static function renderDocument(): void
    {
        echo Self::$document; 
    }

    /**
     * Get html code
     *
     * @return string
     */
    public static function getDocument(): string
    {
        return Self::$document; 
    }
    
    /**
     * Append html code
     *
     * @param string $html
     * @return void
     */
    public static function appendHtml(string $html): void
    {
        if (Self::$append == true) {
            Self::$document .= $html;
        }
    }
}
