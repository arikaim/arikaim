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
 * Utility static functions
 */
class Utils 
{   
    /**
     * Return true if url is valid
     *
     * @param string $url
     * @return boolean
     */
    public static function isValidUrl($url)
    {
        return (filter_var($url,FILTER_VALIDATE_URL) == true) ? true : false; 
    }

    /**
     * Return classes from php code
     *
     * @param string $phpCode
     * @return array
     */
    public static function getClasses($phpCode) 
    {
        $classes = [];
        $tokens = token_get_all($phpCode);
        $count = count($tokens);

        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING && !($tokens[$i - 3] && $i - 4 >= 0 && $tokens[$i - 4][0] == T_ABSTRACT)) {               
                array_push($classes,$tokens[$i][1]);
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
    public static function getParentPath($path)
    {
        if (empty($path) == true) {
            return false;
        }       
        $parentPath = dirname($path);

        return ($parentPath == "." || empty($path) == true) ? false : $parentPath;          
    }

    /**
     * Create random key
     *
     * @return string
     */
    public static function createRandomKey()
    {
        return md5(uniqid(rand(), true));
    }

    /**
     * Return true if ip is valid.
     *
     * @param string $ip
     * @return boolean
     */
    public static function isValidIp($ip)
    {      
        return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) === false) ? false : true;
    }

    /**
     * Check if class implement interface 
     *
     * @param object $obj
     * @param string $interfaceName
     * @return boolean
     */
    public static function isImplemented($obj, $interfaceName)
    {       
        $result = $obj instanceof $interfaceName;
        if ($result == true) {
            return true;
        }
        if (is_object($obj) == false && is_string($obj) == false) {
            return false;
        }

        foreach (class_parents($obj) as $sub_class) {
            if ($result == true) {
                break;
            }
            $result = Self::isImplemented($sub_class, $interfaceName);
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
    public static function constant($name, $default = null)
    {
        return (defined($name) == true) ? constant($name) : $default; 
    }

    /**
     * Convert path to url
     *
     * @param string $path
     * @return void
     */
    public static function convertPathToUrl($path) 
    {
        return str_replace('\\','/',$path);
    }

    /**
     * Return true if text is valid JSON 
     *
     * @param string $text
     * @return boolean
     */
    public static function isJson($jsonText)
    {        
        try {
            return is_array(json_decode($jsonText,true)) ? true : false;
        } catch(\Exception $e) {
            return false;
        }

        return false;
    }
    
    /**
     * Encode array to JSON 
     *
     * @param array $data
     * @return string
     */
    public static function jsonEncode(array $data)
    {
        return json_encode($data,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Clean JSON text
     *
     * @param string $text
     * @return string
     */
    public static function cleanJson($text)
    {
        for ($i = 0; $i <= 31; ++$i) {
            $text = str_replace(chr($i),"",$text);
        }
        $text = str_replace(chr(127),"",$text);
        $text = Self::removeBOM($text);
        $text = stripslashes($text);
        $text = htmlspecialchars_decode($text);

        return $text;
    }

    /**
     * Decode JSON text
     *
     * @param string $text
     * @param boolean $clean
     * @param boolean $toArray
     * @return array
     */
    public static function jsonDecode($text, $clean = true, $toArray = true)
    {        
        $text = ($clean == true) ? Self::cleanJson($text) : $text;

        return json_decode($text,$toArray);
    }

    /**
     * Call static method
     *
     * @param string $class
     * @param string $method
     * @param array|null $args
     * @return mixed
     */
    public static function callStatic($class, $method, $args = null)
    {     
        return (is_callable([$class,$method]) == false) ? null : forward_static_call([$class,$method],$args);
    }

    /**
     * Call object method
     *
     * @param object $obj
     * @param string $method
     * @param array|null $args
     * @return mixed
     */
    public static function call($obj, $method, $args = null)
    {
        if (is_object($obj) == true) {
            $callable = array($obj,$method);
            $class = get_class($obj);
        } else {
            $callable = $method; 
            $class = null;
        }

        if (is_callable($callable) == false) {
            if ($class == null) {
                $class = $obj;
            }
            return Self::callStatic($class,$method,$args);  
        }
        return (is_array($args) == true) ? call_user_func_array($callable,$args) : call_user_func($callable,$args);
    }

    /**
     * Return true if email is valid
     *
     * @param string $email
     * @return boolean
     */
    public static function isEmail($email)
    {
        return (filter_var($email,FILTER_VALIDATE_EMAIL) == false) ? false : true;
    }
    
    /**
     * Check if text contains thml tags
     *
     * @param string $text
     * @return boolean
     */
    public static function hasHtml($text)
    {
        return ($text != strip_tags($text)) ? true : false;
    }

    /**
     * Remove BOM from text
     *
     * @param string $text
     * @return void
     */
    public static function removeBOM($text)
    {        
        return (strpos(bin2hex($text), 'efbbbf') === 0) ? substr($text, 3) : $text;
    }

    /**
     * Check if variable is empty
     *
     * @param mixed $var
     * @return boolean
     */
    public static function isEmpty($var)
    {       
        return (is_object($var) == true) ? empty((array) $var) : empty($var);
    }

    /**
     * Format version text to full version format 0.0.0
     *
     * @param string $text
     * @return string
     */
    public static function formatVersion($text)
    {
        $items = explode('.',trim($text));
        $release = (isset($items[0]) == true) ? $items[0] : $text;
        $major = (isset($items[1]) == true) ? $items[1] : "0";       
        $minor = (isset($items[2]) == true) ? $items[2] : "0";
           
        return "$release.$major.$minor";
    }

    /**
     * Create key 
     *
     * @param string $text
     * @param string $pathItem
     * @param string $separator
     * @return string
     */
    public static function createKey($text, $pathItem = null, $separator = ".")
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
    public static function getValueAsText($value)
    {
        if (gettype($value) == "boolean") {           
            return ($value == true) ? "true" : "false"; 
        }       

        return "\"$value\"";
    }

    /**
     * Return true if variable is Closure
     *
     * @param mixed $variable
     * @return boolean
     */
    public static function isClosure($variable) 
    {
        return (is_object($variable) && ($variable instanceof \Closure));
    }

    /**
     * Create slug
     *
     * @param string $text
     * @param string $separator
     * @return string
     */
    public static function slug($text, $separator = '-')
    {
        return strtolower(preg_replace(
			['/[^\w\s]+/', '/\s+/'],
			['', $separator],
			$text
		));
    } 

    /**
     * Get memory size text.
     *
     * @param integer $size
     * @param array $labels
     * @param boolean $asText
     * @return string|array
     */
    public static function getMemorySizeText($size, $labels = null, $asText = true)
    {        
        if (is_array($labels) == false) {
            $labels = ['Bytes','KB','MB','GB','TB','PB','EB','ZB','YB'];
        }
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        $result['size'] = round($size / pow(1024, $power),2);
        $result['label'] = $labels[$power];

        return ($asText == true) ? $result['size'] . $result['label'] : $result; 
    }

    /**
     * Return base class name
     *
     * @param string|object $class
     * @return string
     */
    public static function getBaseClassName($class)
    {
        $class = is_object($class) ? get_class($class) : $class;
        $tokens = explode('\\',$class);
        
        return end($tokens);
    }

    /**
     * Get script execution time
     *
     * @return integer|false
     */
    public static function getExecutionTime($startTimeConstantName = 'APP_START_TIME') 
    {
        return (defined($startTimeConstantName) == true) ? (microtime(true) - constant($startTimeConstantName)) : false;         
    }
}
