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

use Arikaim\Core\Utils\File;
use \ZipArchive;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;

/**
 * Zip file helpers
 */
class ZipFile 
{
    /**
     * Extract zip arhive
     *
     * @param string $file
     * @param string $destination
     * @param array|string|int|null $files
     * @return bool
     */
    public static function extract(string $file, string $destination, $files = null)
    {
        if (File::exists($file) == false) {
            return false;
        }

        if (File::isWritable($destination) == false) {
            File::setWritable($destination);
        }

        $zip = new ZipArchive;

        $result = $zip->open($file);
        if ($result !== true) {
            return false;
        }
        if (\is_integer($files) == true) {
            $item = $zip->getNameIndex($files);
            $files = [$item];
           
        }
        $result = $zip->extractTo($destination,$files);
        $zip->close(); 

        return $result;
    }

    /**
     * Get zip file item name
     *
     * @param string $zipFile
     * @param int $index
     * @return string|null
     */
    public static function getItemPath(string $zipFile, int $index): ?string
    {
        $zip = new ZipArchive;
        $result = $zip->open($zipFile);
        if ($result !== true) {
            return null;
        }

        return $zip->getNameIndex($index);    
    }

    /**
     * Create zip arhive
     *
     * @param string $source
     * @param string $destination
     * @param array  $skipDir
     * @return boolean
     */
    public static function create(string $source, string $destination, array $skipDir = []): bool
    { 
        $zip = new ZipArchive();
        if ($zip->open($destination,ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {          
            return false;
        }

        if (\is_dir($source) == true) {
            $iterator = new RecursiveDirectoryIterator($source);        
            $files = new RecursiveIteratorIterator($iterator,RecursiveIteratorIterator::LEAVES_ONLY);

            foreach ($files as $file) {                          
                if ($file->isDir() == true) {       
                    $path = $file->getRealPath() . DIRECTORY_SEPARATOR;
                    $relativePath = \str_replace($source,'',$path);
                    $relativePath = (empty($relativePath) == true) ? DIRECTORY_SEPARATOR : $relativePath;
                    $tokens = \explode(DIRECTORY_SEPARATOR,$relativePath);
                    // skip dir                  
                    if (\in_array($tokens[0],$skipDir) == true) { 
                        continue;
                    }
                    $zip->addGlob($path . '*.*',GLOB_BRACE,[
                        'add_path'        => $relativePath,
                        'remove_all_path' => true
                    ]);  

                    
                    $zip->addGlob($path . '.htaccess',GLOB_BRACE,[
                        'add_path'        => $relativePath,
                        'remove_all_path' => true
                    ]);    
                    $zip->addGlob($path . '.gitkeep',GLOB_BRACE,[
                        'add_path'        => $relativePath,
                        'remove_all_path' => true
                    ]);  
                    $zip->addGlob($path . 'cli',GLOB_BRACE,[
                        'add_path'        => $relativePath,
                        'remove_all_path' => true
                    ]);                  
                }
            }
        } else {
            $zip->addFile($source);
        }

        // close if not empty
        if ($zip->numFiles > 0) {
            $zip->close();
        }
      
        return ($zip->status == ZIPARCHIVE::ER_OK);      
    }

    /**
     * Check if zip arhive is valid
     *
     * @param string $file
     * @return boolean
     */
    public static function isValid(string $file): bool
    {      
        $zip = new ZipArchive();
        $result = $zip->open($file,ZipArchive::CHECKCONS);

        switch($result) {
            case ZipArchive::ER_NOZIP:
                return false;
            case ZipArchive::ER_INCONS:
                return false;
            case ZipArchive::ER_CRC:
                return false;
        }      

        return true;
    }    

    /**
     * Get zip error
     *
     * @param mixed $resource
     * @return string|null
     */
    public static function getZipError($resource): ?string
    {
        switch($resource) {
            case ZipArchive::ER_NOZIP :
                return 'Not a zip archive';              
            case ZipArchive::ER_INCONS :
                return 'Consistency check failed';               
            case ZipArchive::ER_CRC :
                return 'Checksum failed';                          
        }   

        return null;
    }
}
