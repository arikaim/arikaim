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

use Arikaim\Core\Utils\Text;
use Exception;

/**
 * Utility static functions
 */
class Utils 
{   
    /**
     * Return true if required version = current or < 
     *
     * @param string $currentVersion
     * @param string $requiredVersion
     * @param string|null $operator
     * @return boolean
     */
    public static function checkVersion(string $currentVersion, string $requiredVersion, ?string $operator = null): bool
    {
        $currentVersion = Self::formatVersion($currentVersion);
        $requiredVersion = Self::formatVersion($requiredVersion);
        if (empty($operator) == true) {
            $result = \version_compare($currentVersion,$requiredVersion);
            return ($result == 0 || $result == 1);
        }
        
        return \version_compare($currentVersion,$requiredVersion,$operator);
    }

    /**
     * Return true if url is valid
     *
     * @param string $url
     * @return boolean
     */
    public static function isValidUrl(string $url): bool
    {
        return (\filter_var($url,FILTER_VALIDATE_URL) !== false);
    }

    /**
     * Return classes from php code
     *
     * @param string $phpCode
     * @return array
     */
    public static function getClasses(string $phpCode): array 
    {
        $classes = [];
        $tokens = \token_get_all($phpCode);
        $count = \count($tokens);

        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS 
                && $tokens[$i - 1][0] == T_WHITESPACE 
                && $tokens[$i][0] == T_STRING 
                && !($tokens[$i - 3] 
                && $i - 4 >= 0 
                && $tokens[$i - 4][0] == T_ABSTRACT)) {               
                $classes[] = $tokens[$i][1];
            }
        }

        return $classes;
    }

    /**
     * Get parent path
     *
     * @param string $path
     * @return string|false
     */
    public static function getParentPath(string $path)
    {
        if (empty($path) == true) {
            return false;
        }       
        $parentPath = \dirname($path);

        return ($parentPath == '.') ? false : $parentPath;          
    }

    /**
     * Create random key
     *
     * @return string
     */
    public static function createRandomKey(): string
    {
        return \md5(\uniqid(\rand(),true));
    }

    /**
     * Create unique token
     *
     * @param string $prefix
     * @param bool $long
     * @return string
     */
    public static function createToken(string $prefix = '', bool $long = false): string
    {
        $hash = \md5(\rand(1,10) . \microtime());
        $secondHash = \md5(\rand(1,10) . \microtime());
        $token = $prefix . $hash;
        
        return ($long == true) ? $token . '-' . $secondHash : $token;
    }
    
    /**
     * Return true if ip is valid.
     *
     * @param string $ip
     * @return boolean
     */
    public static function isValidIp(string $ip): bool
    {      
        return (\filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) !== false);
    }

    /**
     * Check if class implement interface 
     *
     * @param object $obj
     * @param string $interfaceName
     * @return boolean
     */
    public static function isImplemented($obj, string $interfaceName): bool
    {       
        $result = $obj instanceof $interfaceName;
        if ($result == true) {
            return true;
        }
        if (\is_object($obj) == false && \is_string($obj) == false) {
            return false;
        }

        foreach (\class_parents($obj) as $subClass) {
            if ($result == true) {
                break;
            }
            $result = Self::isImplemented($subClass, $interfaceName);
        } 

        return $result;
    }

    /**
     * Return constant value or default if constant not defined.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function constant(string $name, $default = null)
    {
        return (\defined($name) == true) ? \constant($name) : $default; 
    }

    /**
     * Convert path to url
     *
     * @param string $path
     * @return string
     */
    public static function convertPathToUrl(string $path): string 
    {
        return \str_replace('\\','/',$path);
    }

    /**
     * Return true if text is valid JSON 
     *
     * @param string|null $text
     * @return boolean
     */
    public static function isJson(?string $jsonText): bool
    {        
        if (empty($jsonText) == true) {
            return false;
        }
        try {           
            return \is_array(\json_decode($jsonText,true));         
        } catch(Exception $e) {
            return false;
        }

        return false;
    }
    
    /**
     * Encode array to JSON 
     *
     * @param array|null $data
     * @return string|null
     */
    public static function jsonEncode(?array $data): ?string
    {
        if (\is_aray($data) == false) {
            return null;
        }

        return \json_encode(
            $data,
            JSON_PRETTY_PRINT | 
            JSON_UNESCAPED_UNICODE | 
            JSON_UNESCAPED_SLASHES |
            JSON_NUMERIC_CHECK 
        );
    }

    /**
     * Clean JSON text
     *
     * @param string $text
     * @return string
     */
    public static function cleanJson(string $text): string
    {
        for ($i = 0; $i <= 31; ++$i) {
            $text = \str_replace(\chr($i),'',$text);
        }
        $text = \str_replace(\chr(127),'',$text);
        $text = Self::removeBOM($text);
        $text = \stripslashes($text);
        $text = \htmlspecialchars_decode($text);

        return $text;
    }

    /**
     * Decode JSON text
     *
     * @param string|null $text
     * @param boolean $clean
     * @param boolean|null $associative
     * @return array
     */
    public static function jsonDecode(?string $text, bool $clean = true, ?bool $associative = true)
    {        
        if (empty($text) == true) {
            return [];
        }
        $text = ($clean == true) ? Self::cleanJson($text) : $text;

        return \json_decode($text,$associative);
    }

    /**
     * Call static method
     *
     * @param string $class
     * @param string $method
     * @param array|null $args
     * @return mixed
     */
    public static function callStatic(string $class, string $method, ?array $args = null)
    {     
        return (\is_callable([$class,$method]) == false) ? null : \forward_static_call([$class,$method],$args);
    }

    /**
     * Call object method
     *
     * @param object $obj
     * @param string $method
     * @param array|null $args
     * @return mixed
     */
    public static function call($obj, ?string $method, ?array $args = null)
    {
        if (\is_object($obj) == true) {
            $callable = [$obj,$method];
            $class = \get_class($obj);
        } else {
            $callable = $method; 
            $class = null;
        }

        if (\is_callable($callable) == false) {
            if ($class == null) {
                $class = $obj;
            }
            return Self::callStatic($class,$method,$args);  
        }
        return (\is_array($args) == true) ? \call_user_func_array($callable,$args) : \call_user_func($callable,$args);
    }

    /**
     * Return true if email is valid
     *
     * @param string $email
     * @return boolean
     */
    public static function isEmail(string $email): bool
    {
        return (\filter_var($email,FILTER_VALIDATE_EMAIL) !== false);
    }
    
    /**
     * Check if text contains thml tags
     *
     * @param string $text
     * @return boolean
     */
    public static function hasHtml(string $text): bool
    {
        return ($text != \strip_tags($text));
    }

    /**
     * Remove BOM from text
     *
     * @param string $text
     * @return string
     */
    public static function removeBOM(string $text): ?string
    {        
        return (\strpos(\bin2hex($text),'efbbbf') === 0) ? \substr($text,3) : $text;
    }

    /**
     * Check if variable is empty
     *
     * @param mixed $var
     * @return boolean
     */
    public static function isEmpty($var): bool
    {       
        return (\is_object($var) == true) ? empty((array)$var) : empty($var);
    }

    /**
     * Format version to full version format 0.0.0
     *
     * @param string|null $version
     * @return string
     */
    public static function formatVersion(?string $version): string
    {
        $version = $version ?? '1.0.0';
        $items = \explode('.',\trim($version));
        $release = $items[0] ?? $version;
        $major = $items[1] ?? '0';       
        $minor = $items[2] ?? '0';
           
        return $release . '.' . $major . '.' . $minor;
    }

    /**
     * Create key 
     *
     * @param string $text
     * @param string $pathItem
     * @param string $separator
     * @return string
     */
    public static function createKey(string $text, ?string $pathItem = null, string $separator = '.'): string
    {
        return (empty($pathItem) == true) ? $text : $text . $separator . $pathItem;     
    }

    /**
     * Return default if variable is empty
     *
     * @param mixed $variable
     * @param mixed $default
     * @return mixed
     */
    public function getDefault($variable, $default)
    {
        return (Self::isEmpty($variable) == true) ? $default : $variable;      
    }

    /**
     * Convert value to text
     *
     * @param mixed $value
     * @return string
     */
    public static function getValueAsText($value): string
    {
        if (\gettype($value) == 'boolean') {           
            return ($value == true) ? 'true' : 'false'; 
        }   
        if ($value === null) {
            return 'null';
        }

        return '\'' . (string)$value . '\'';
    }

    /**
     * Return true if variable is Closure
     *
     * @param mixed $variable
     * @return boolean
     */
    public static function isClosure($variable): bool 
    {
        return (\is_object($variable) && ($variable instanceof \Closure));
    }

    /**
     * Return true if text is utf8 encoded string
     *
     * @param mixed $text
     * @return boolean
     */
    public static function isUtf($text): bool 
    {
        return (bool)\preg_match("//u",\serialize($text));
    }

    /**
     * Create slug
     *
     * @param string $text
     * @param string $separator
     * @return string
     */
    public static function slug(string $text, string $separator = '-'): string
    {
        if (Self::isUtf($text) == true) {            
            $text = \trim(\mb_strtolower($text));
            // Replace umlauts chars
            $text = Text::replaceChars($text);
            $text = \str_replace(' ',$separator,$text);
            return $text;
        }
        $text = \trim(\strtolower($text));
        // Replace umlauts chars
        $text = Text::replaceChars($text);

        return \preg_replace(["/[^\w\s]+/", "/\s+/"],['',$separator],$text);
    } 

    /**
     * Get memory size text.
     *
     * @param integer $size
     * @param array $labels
     * @param boolean $asText
     * @return string|array
     */
    public static function getMemorySizeText($size, ?string $labels = null, bool $asText = true)
    {        
        $labels = (\is_array($labels) == false) ? ['Bytes','KB','MB','GB','TB','PB','EB','ZB','YB'] : $labels;            
        $power = $size > 0 ? \floor(\log($size, 1024)) : 0;
        $result['size'] = \round($size / \pow(1024, $power),2);
        $result['label'] = $labels[$power];

        return ($asText == true) ? $result['size'] . ' ' . $result['label'] : $result; 
    }

    /**
     * Convert to bytes
     *
     * @param string|int $size
     * @return integer|null
     */
    public static function convertToBytes($size): ?int
    {
        $labels = ['bytes' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4];
        $unit = \strtoupper(\trim(\substr($size,-2)));
        if ((int)$unit !== 0) {
            $unit = 'bytes';
        }
        if (\in_array($unit,\array_keys($labels)) == false) {
            return null;
        }

        $value = \intval( \trim(\substr($size,0,\strlen($size) - 2)) );

        return ($value == 0) ? null : $value * \pow(1024, $labels[$unit]);      
    }
    
    /**
     * Return base class name
     *
     * @param string|object $class
     * @return string
     */
    public static function getBaseClassName($class)
    {
        $class = \is_object($class) ? \get_class($class) : $class;
        $tokens = \explode('\\',$class);
        
        return \end($tokens);
    }

    /**
     * Get script execution time
     *   
     * @return float
     */
    public static function getExecutionTime() 
    { 
        return (\microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'] ?? 0);  
    }
}
