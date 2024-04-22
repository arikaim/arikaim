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

use ErrorException;

/**
 * File
*/
class File 
{ 
    /**
     * Replace file name witout file extension
     *
     * @param string $fileName
     * @param string $baseName
     * @return string
     */
    public static function replaceFileName(string $fileName, string $newFileName): string
    {
        $parts = \pathinfo($fileName); 
        $fileName = \pathinfo($newFileName,PATHINFO_FILENAME) . '.' . $parts['extension'];

        if (empty($parts['dirname']) == true || $parts['dirname'] == '.') {
            return $fileName;
        }
        
        return $parts['dirname'] . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * Generate unique filename
     *
     * @param string $fileName
     * @return string
     */
    public static function createUniqueFileName(string $fileName): string
    {
        $parts = \pathinfo($fileName);
       
        return ($parts['dirname'] ?? '') . DIRECTORY_SEPARATOR . $parts['filename'] . '-' . \Arikaim\Core\Utils\Uuid::create() . '.' . $parts['extension'];
    }

    /**
     * Load json file and return decoded array
     *
     * @param string $fileName
     * @param array|null $vars     
     * @return array|false
     */
    public static function readJsonFile(string $fileName, ?array $vars = null) 
    {    
        try {
            $json = \file_get_contents($fileName);   
        } catch (\Exception $e) {
            return false;
        }
      
        if (empty($vars) == false) {
            $json = \Arikaim\Core\Utils\Text::render($json,$vars);
        }     

        $data = \json_decode($json,true);
        
        return ($data == null) ? [] : $data;       
    }

    /**
     * Get php classes defined in file
     *
     * @param string $fileName
     * @return array|false
     */
    public static function getClassesInFile(string $fileName) 
    {
        if (\file_exists($fileName) == false) {
            return false;
        }
        $code = \file_get_contents($fileName);

        return \Arikaim\Core\Utils\Utils::getClasses($code);
    }

    /**
     * Check if file exists
     *
     * @param string $fileName
     * @return bool
     */
    public static function exists(string $fileName): bool 
    {
        return \file_exists($fileName);           
    }

    /**
     * Return true if file is writtable
     *
     * @param string $fileName
     * @return boolean
     */
    public static function isWritable(string $fileName): bool 
    {
        return (bool)\is_writable($fileName);
    }

    /**
     * Set file writtable
     *
     * @param string $fileName
     * @return boolean
     */
    public static function setWritable(string $fileName): bool 
    {
        if (\file_exists($fileName) == false) {
            return false;
        }      
        
        try {
            $result = @chmod($fileName,0777);  
        } catch (ErrorException $e) {
            $user = @exec('whoami');
            @chown($fileName,$user);
            $result = @chmod($fileName,0777);  
        }

        return (bool)$result;
    }

    /**
     * Return file size
     *
     * @param string $fileName
     * @return integer|false
     */
    public static function getSize(string $fileName)
    {
        return (\file_exists($fileName) == false) ? false : \filesize($fileName);          
    }

    /**
     * Get file size text.
     *
     * @param integer $size
     * @param array $labels
     * @param boolean $asText
     * @return string|array
     */
    public static function getSizeText($size, $labels = null, $asText = true)
    {        
        return \Arikaim\Core\Utils\Utils::getMemorySizeText($size,$labels,$asText);      
    }

    /**
     * Create directory
     *
     * @param string $path
     * @param integer $mode
     * @param boolean $recursive
     * @return boolean
     */
    public static function makeDir(string $path, $mode = 0755, bool $recursive = true): bool
    {
        return (\file_exists($path) == true) ? Self::setWritable($path,$mode) : (bool)\mkdir($path,$mode,$recursive);                 
    }

    /**
     * Undocumented function
     *
     * @param array $file
     * @param string $path
     * @param integer $mode
     * @param integer $flags
     * @return boolean
     */
    public static function writeUplaodedFile(array $file, string $path, $mode = null, $flags = 0)
    {
        $fileName = $path . $file['name'];
        $data = \explode(',',$file['data']);
        $result = Self::writeEncoded($fileName,$data[1],$flags);
        if ($result != false && $mode != null) {
            \chmod($fileName,$mode);
        }

        return $result;
    }

    /**
     * Write encoded file
     *
     * @param string $fileName
     * @param mixed $encodedData
     * @param integer $flags
     * @return boolean
     */
    public static function writeEncoded(string $fileName, $encodedData, $flags = 0): bool
    {
        $data = \base64_decode($encodedData);

        return (bool)Self::write($fileName,$data,$flags);
    }

    /**
     * Write file
     *
     * @param string $fileName
     * @param mixed $data
     * @param integer $flags
     * @return boolean
     */
    public static function write(string $fileName, $data, $flags = 0): bool
    {
        $result = \file_put_contents($fileName,$data,$flags);
        
        return ($result !== false);
    }

    /**
     * Get file base name from path
     *
     * @param string|null $path
     * @param string $suffix
     * @return string|null
     */
    public static function baseName(?string $path, string $suffix = '')
    {
        if (empty($path) == true) {
            return $path;
        }

        return \basename($path,$suffix);
    }

    /**
     * Remove file extension
     *
     * @param string $fileName
     * @return string
     */
    public static function removeExtension(string $fileName): string
    {
        $pos = \strrpos($fileName,'.');

        return ($pos > 0) ? \substr($fileName,0,$pos) : $fileName;
    }

    /**
     * Replace file extension
     *
     * @param string $fileName
     * @param string $extension
     * @return string
     */
    public static function replaceExtension(string $fileName, string $extension): string
    {
        return Self::removeExtension($fileName) . '.' . $extension;
    }

    /**
     * Return file extension
     *
     * @param string $fileName
     * @return string
     */
    public static function getExtension(string $fileName): string
    {
        return \pathinfo($fileName,PATHINFO_EXTENSION);
    }

    /**
     * Delete file or durectiry
     *
     * @param string $fileName
     * @return bool
     */
    public static function delete(string $fileName)
    {
        if (\file_exists($fileName) == true) {
            return (\is_dir($fileName) == true) ? Self::deleteDirectory($fileName) : \unlink($fileName);          
        }

        return false;
    }

    /**
     * Return true if direcotry is empty
     *
     * @param string $path
     * @return boolean
     */
    public static function isEmpty(string $path): bool
    {
        return (\count(\glob($path . "/*")) === 0);
    }
    
    /**
     * Delete directory and all sub directories
     *
     * @param string $path
     * @param bool $skipMainFolder
     * @return bool
     */
    public static function deleteDirectory(string $path, bool $skipMainFolder = true)
    {
        if (\file_exists($path) === false) {
            return false;
        }
    
        $dir = new \RecursiveDirectoryIterator($path,\RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($dir,\RecursiveIteratorIterator::CHILD_FIRST);

        $result = true;
        foreach ($iterator as $file) {
            if (\substr($file->getBasename(),0,1) == '.') {
                continue;
            }
              
            Self::setWritable($file->getRealPath());
          
            if ($file->isDir() == true) {
                if (Self::isEmpty($file->getRealPath()) == false) {
                    $result = Self::deleteDirectory($file->getRealPath());
                } else {
                    if (\rmdir($file->getRealPath()) == false) {
                        $result = false;
                    }; 
                }         
            } else {                            
                if (\unlink($file->getRealPath()) == false) {
                    $result = false;
                };
            }
        }

        if ($skipMainFolder == false) {
            \rmdir($path);
        }
     
        return $result;
    }

    /**
     * Read file
     *
     * @param string $fileName
     * @return mixed|null
     */
    public static function read(string $fileName)
    {
        return (\file_exists($fileName) == true) ? \file_get_contents($fileName) : null;           
    }

    /**
     * Return true if MIME type is image
     *
     * @param string $mimeType
     * @return boolean
     */
    public static function isImageMimeType(string $mimeType)
    {
        return (\substr($mimeType,0,5) == 'image');
    }

    /**
     * Get file mime type
     *
     * @param string $fileName
     * @return string|false
     */
    public static function getMimetype(string $fileName)
    {
        return \mime_content_type($fileName);
    }

    /**
     * Copy file, symlink or directory
     *
     * @param string $from
     * @param string $to
     * @param boolean $overwrite
     * @return boolean
     */
    public static function copy(string $from, string $to, bool $overwrite = true): bool
    {
        if (\is_link($from) == true) {
            return (bool)\symlink(\readlink($from),$to);
        }
        if (\is_file($from) == true) {
            if ($overwrite == false) {
                if (\file_exists($to) == true) {
                    return false;
                }
            }
            return \copy($from,$to);
        }
        if (\is_dir($to) == false) {
            \mkdir($to);
        }

        $dir = \dir($from);
        while (false !== $item = $dir->read()) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            // copy sub directories
            $result = Self::copy($from . DIRECTORY_SEPARATOR . $item,$to . DIRECTORY_SEPARATOR . $item);
            if ($result === false) {
                return false;
            }
        }       
        $dir->close();

        return true;
    }

    /**
     * Get directory files
     *
     * @param string $path
     * @param array $skip
     * @return array
     */
    public static function scanDir(string $path, array $skip = ['..','.'])
    {      
        $items = (\is_dir($path) == false) ? [] : \scandir($path);

        return \array_diff($items,$skip);
    }
}
