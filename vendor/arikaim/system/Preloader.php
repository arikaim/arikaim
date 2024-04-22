<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\System;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use FilesystemIterator;

/**
 * Opcache preloader 
 */
class Preloader
{
    /**
     * Show output
     *
     * @var bool
     */
    private $verbose;

    /**
     * Constructor
     *
     * @param array $files
     * @param bool $verbose
     */
    public function __construct(array $files = [], bool $verbose = false)
    {
        $this->verbose = $verbose;

        foreach ($files as $file) {
            $this->load($file);
        }
    }
    
    /**
     * Load file or path
     *
     * @param string $file
     * @return object
     */
    public function load(string $file): object
    {
        if (\is_dir($file)) {
            $this->loadPath($file);
            return $this;
        } 
        
        if (\file_exists($file) == false) {
            $this->message('File not exist: ' . $file);
            return $this;
        }

        require_once($file);
        $this->message('File loaded: ' . $file);           
    
        return $this;
    }

    /**
     * Load all php files in path (recursive)
     *
     * @param string $path
     * @return void
     */
    public function loadPath(string $path): void
    {
        $dir = new RecursiveDirectoryIterator($path,FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dir);
        $phpFiles = new RegexIterator($iterator,'/^.+\.php$/i',RecursiveRegexIterator::GET_MATCH);

        foreach ($phpFiles as $file) { 
            require_once($file[0]);    
            $this->message('File loaded: ' . $file[0]);
        }
    }

    /**
     * Get preloaded files
     *
     * @return array
     */
    public static function getCachedFiles(): array
    {
        return \opcache_get_status()['scripts'] ?? [];
    }

    /**
     * Show message
     *
     * @param string $message
     * @return void
     */
    protected function message(string $message): void
    {
        if ($this->verbose == true) {
            echo $message . "\n";
        }
    }
}
