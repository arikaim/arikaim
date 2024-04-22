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

use Arikaim\Core\Collection\Collection;
use Arikaim\Core\System\Traits\PhpConfigFile;

/**
 * Config file loader and writer
 */
class Config extends Collection
{
    use PhpConfigFile;
 
    /**
     * Config file name
     *
     * @var string
     */
    private $fileName;
    
    /**
     * Config files directory
     *
     * @var string
     */
    private $configDir;

    /**
     * List read protected var keys
     *
     * @var array
     */
    private $readProtectedKeys = [];

    /**
     * List write protected var keys
     *
     * @var array
     */
    private $writeProtectedKeys = [];

    /**
     * Constructor
     *
     * @param string|null $fileName
     * @param string|null $path
     */
    public function __construct(?string $fileName = 'config.php', ?string $path = null) 
    {             
        $this->fileName = $fileName;
        $this->configDir = $path ?? '';
       
        $data = $this->load($fileName);
        parent::__construct($data);   

        $this->setComment('database settings','db');
        $this->setComment('application settings','settings');
    }
    
    /**
     * Set read protecetd vars keys
     *
     * @param array $keys
     * @return void
     */
    public function setReadProtectedVars(array $keys): void
    {
        $this->readProtectedKeys = $keys;
    }

    /**
     * Set write protecetd vars keys
     *
     * @param array $keys
     * @return void
     */
    public function setWriteProtectedVars(array $keys): void
    {
        $this->writeProtectedKeys = $keys;
    }

    /**
     * Return true if var is not read protected
     *
     * @param string $key
     * @return boolean
     */
    public function hasReadAccess(string $key): bool
    {
        return (\in_array($key,$this->readProtectedKeys) == false);
    }

    /**
     * Return true if var is not write protected
     *
     * @param string $key
     * @return boolean
     */
    public function hasWriteAccess(string $key): bool
    {
        return (\in_array($key,$this->writeProtectedKeys) == false);
    }

    /**
     * Get config file name
     *
     * @return string
     */
    public function getConfigFile(): string
    {
        return $this->configDir . $this->fileName;
    }

    /**
     * Reload config file
     *
     * @return void
     */
    public function reloadConfig(): void
    {
        $config = $this->includePhpArray($this->configDir . $this->fileName);
        $this->data = (\is_array($config) == true) ? $config : $this->load($this->fileName);         
    }

    /**
     * Set config dir
     *
     * @param string $dir
     * @return void
     */
    public function setConfigDir(string $dir): void 
    {
        $this->configDir = $dir;
    }

    /**
     * Read config file
     *
     * @param string $fileName
     * @param string $configDir
     * @return Collection
     */
    public static function read(string $fileName, string $configDir) 
    {
        return new Self($fileName,null,$configDir);      
    }

    /**
     * Load config file
     *
     * @param string $fileName
     * @return array
     */
    public function load(string $fileName): array 
    {       
        $fullFileName = $this->configDir . $fileName;
       
        return (\file_exists($fullFileName) == true) ? include($fullFileName) : [];              
    }   

    /**
     * Save config file
     *
     * @param string|null $fileName
     * @param array|null $data
     * @return bool
     */
    public function save(?string $fileName = null, ?array $data = null): bool
    {
        $fileName = (empty($fileName) == true) ? $this->fileName : $fileName;
        $data = (\is_array($data) == true) ? $data : $this->data;

        return $this->saveConfigFile($this->configDir . $fileName,$data);           
    }

    /**
     * Load json config file
     *
     * @param string $fileName
     * @return array
     */
    public function loadJsonConfigFile(string $fileName): array
    {
        $data = \Arikaim\Core\Utils\File::readJsonFile($this->configDir . $fileName);
        
        return ($data === false) ? [] : $data;
    }

    /**
     * Check if file exist
     *
     * @param string $fileName
     * @return boolean
     */
    public function hasConfigFile(string $fileName): bool
    {
        return (bool)\file_exists($this->configDir . $fileName);
    }
}
