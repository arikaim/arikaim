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
     * @param string $path
     * @return integer
     */
    public static function extract($file, $path)
    {
        if (File::exists($file) == false) {
            return false;
        }

        if (File::isWritable($path) == false) {
            File::setWritable($path);
        }


        $zip = new ZipArchive;
        $result = $zip->open($file);
        if ($result !== true) {
            return false;
        }
        $result = $zip->extractTo($path);
        $zip->close(); 

        return $result;
    }

    /**
     * Create zip arhive
     *
     * @param string $source
     * @param string $destination
     * @param array  $skipDir
     * @return boolean
     */
    public static function create($source, $destination, $skipDir = [])
    { 
        $zip = new ZipArchive();
        if ($zip->open($destination,ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {          
            return false;
        }

        if (is_dir($source) == true) {
            $iterator = new RecursiveDirectoryIterator($source);        
            $files = new RecursiveIteratorIterator($iterator,RecursiveIteratorIterator::LEAVES_ONLY);

            foreach ($files as $file) {                          
                if ($file->isDir() == true) {       
                    $path = $file->getRealPath() . DIRECTORY_SEPARATOR;
                    $relativePath = str_replace($source,'',$path);
                    $relativePath = (empty($relativePath) == true) ? DIRECTORY_SEPARATOR : $relativePath;
                    $tokens = explode(DIRECTORY_SEPARATOR,$relativePath);
                    // skip dir
                    if (in_array($tokens[0],$skipDir) == true) { 
                        continue;
                    }
                    $zip->addGlob($path . '*.*',GLOB_BRACE,[
                        'add_path' => $relativePath,
                        'remove_all_path' => true
                    ]);                  
                }
            }
        } else {
            $zip->addFile($source);
        }
        $zip->close();

        return ($zip->status == ZIPARCHIVE::ER_OK);      
    }

    /**
     * Check if zip arhive is valid
     *
     * @param string $file
     * @return boolean
     */
    public static function isValid($file)
    {
        $error = null;
        $zip = new ZipArchive();

        $result = $zip->open($file, ZipArchive::CHECKCONS);
        switch($result) {
            case \ZipArchive::ER_NOZIP :
                $error = 'Not a zip archive';
                break;
            case \ZipArchive::ER_INCONS :
                $error = 'Consistency check failed';
                break;
            case \ZipArchive::ER_CRC :
                $error= 'Checksum failed';
                break;
        }      

        return ($error == null) ? true : false;
    }    
}
