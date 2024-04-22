<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Storage;

use League\Flysystem\MountManager;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

use Arikaim\Core\Utils\Path;
use Arikaim\Core\Interfaces\StorageInterface;
use Exception;

/**
 * Storage module class
 */
class Storage implements StorageInterface
{
    const ROOT_FILESYSTEM_NAME = 'storage';
    const USER_FILESYSTEM_NAME = 'user-storage';
    const RECYCLE_BIN_PATH     = 'bin' . DIRECTORY_SEPARATOR;

    /**
     * Mount manager obj ref
     *
     * @var MountManager
     */
    private $manager;

    /**
     * System directories
     *
     * @var array
     */
    protected $systemDir = [
        'backup',
        'public',
        'repository',
        'temp',
        'bin'
    ];
    
    /**
     * Local filesystem names
     *
     * @var array
     */
    protected $localFilesystems = ['storage','user-storage'];

    /**
     * Error message
     *
     * @var string|null
     */
    protected $errorMessage = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->manager = new MountManager();
        $this->boot();
    }

    /**
     * Get relative storage path
     *
     * @param boolean $public
     * @return string
     */
    public function getStorageRelativePath(bool $public = false): string
    {
        return ($public == false) ? 
            Path::getRelativePath(Path::STORAGE_PATH,false) :
            Path::getRelativePath(Path::STORAGE_PUBLIC_PATH,false);
    }

    /**
     * Get error
     *
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Install module
     *
     * @return boolean
     */
    public function install(): bool
    {
        if (\file_exists(Path::STORAGE_PATH) == false) {
            \mkdir(Path::STORAGE_PATH,0755,true);
        };
        if (\file_exists(Path::STORAGE_PATH . 'repository') == false) {
            \mkdir(Path::STORAGE_PATH . 'repository',0755,true);           
        };

        return true;
    }

    /**
     * Boot module
     *
     * @return void
     */
    public function boot(): void
    {
        $localAdapter = new Local(Path::STORAGE_PATH);
        $this->mount(Self::ROOT_FILESYSTEM_NAME,$localAdapter);      
    }

    /**
     * Mount local filesystem
     *
     * @param string $name
     * @param string|null $path
     * @return MountManager|false
     */
    public function mountLocal(string $name, ?string $path = null)
    {
        $path = (empty($path) == true) ? Path::STORAGE_PATH : $path;
        $adapter = new Local($path);

        return $this->mount($name,$adapter);
    }

    /**
     * Mount filesystem
     *
     * @param string $name
     * @param object|string $adapter  Adapter object or driver name
     * @return MountManager|false
     */
    public function mount(string $name, $adapter)
    {
        if (\is_object($adapter) == false) {
            return false;
        }
      
        return $this->manager->mountFilesystem($name,new Filesystem($adapter));
    }

    /**
     * Mount filesystem
     *
     * @param string $name
     * @param Filesystem $filesystem
     * @return bool
     */
    public function mountFilesystem(string $name, $filesystem): bool
    {
        return \is_object($this->manager->mountFilesystem($name,$filesystem));
    }

    /**
     * Get filesystem
     *
     * @param string $name
     * @return \League\Flysystem\FilesystemInterface|null
     */
    public function get(string $name): ?object
    {
        return $this->manager->getFilesystem($name);
    }

    /**
     * Get full file path
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return string
     */
    public function getFullPath(string $path = '', ?string $fileSystemName = null): string
    {      
        return $this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->getAdapter()->getPathPrefix() . $path;
    }

    /**
     * Get directory contents
     *
     * @param string $path
     * @param boolean $recursive
     * @param string|null $fileSystemName
     * @return array|false
     */
    public function listContents(string $path = '', bool $recursive = false, ?string $fileSystemName = null)
    {             
        return $this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->listContents($path,$recursive);      
    }

    /**
     * Return true if directory is empty
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return boolean
     */
    public function isEmpty(string $path, ?string $fileSystemName = null): bool
    {
        return empty($this->listContents($path,false,$fileSystemName ?? Self::ROOT_FILESYSTEM_NAME));
    }

    /**
     * Write files
     *
     * @param string $path
     * @param string $contents
     * @param array $config
     * @param string|null $fileSystemName
     * @return bool 
     */
    public function write(string $path, $contents, $config = [], ?string $fileSystemName = null): bool
    {
        $fileSystemName = $fileSystemName ?? Self::ROOT_FILESYSTEM_NAME;

        if ($this->has($path,$fileSystemName) == true) {
            return (bool)$this->update($path,$contents,$config,$fileSystemName);
        } 
        
        return (bool)$this->get($fileSystemName)->write($path,$contents,$config);                 
    }

    /**
     * Write file stream
     *
     * @param string $path
     * @param resource $resource
     * @param array $config
     * @param string|null $fileSystemName
     * @return bool
     */
    public function writeStream(string $path, $resource, $config = [], ?string $fileSystemName = null): bool
    {        
        $fileSystemName = $fileSystemName ?? Self::ROOT_FILESYSTEM_NAME;

        if ($this->has($path,$fileSystemName) == true) {
            return (bool)$this->get($fileSystemName)->updateStream($path,$resource,$config);        
        }
        
        return (bool)$this->get($fileSystemName)->writeStream($path,$resource,$config);        
    }

    /**
     * Update files
     *
     * @param string $path
     * @param string $contents
     * @param array $config
     * @param string|null $fileSystemName
     * @return bool 
     */
    public function update(string $path, $contents, array $config = [], ?string $fileSystemName = null): bool
    {
        return (bool)$this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->update($path,$contents,$config);                  
    }

    /**
     * Update files using a stream
     *
     * @param string $path
     * @param resource $contents
     * @param array $config
     * @param string|null $fileSystemName
     * @return bool 
     */
    public function updateStream(string $path, $resource, $config = [], ?string $fileSystemName = null): bool
    {
        return (bool)$this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->updateStream($path,$resource,$config);              
    }

    /**
     * Read file
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return string|false
     */
    public function read(string $path, ?string $fileSystemName = null)
    {
        try {
            $this->errorMessage = null;
            return $this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->read($path);
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    /**
     * Read file as a stream
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return resource|false
     */
    public function readStream(string $path, ?string $fileSystemName = null)
    {
        try {
            $this->errorMessage = null;
            return $this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->readStream($path);
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
       
    }

    /**
     * Delete all files in direcotry (recursive)
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return void
     */
    public function deleteFiles(string $path, ?string $fileSystemName = null)
    {
        $fileSystemName = $fileSystemName ?? Self::ROOT_FILESYSTEM_NAME;
        $files = $this->listContents($path,true,$fileSystemName);

        foreach ($files as $item) {
            if ($item['type'] == 'dir') {             
                if ($this->has($item['path'],$fileSystemName) == true) {
                    $this->deleteDir($path,$fileSystemName);
                }
            }           
            if ($this->has($item['path'],$fileSystemName) == true) {
                $this->delete($item['path'],$fileSystemName);
            }            
        }       
    }

    /**
     * Move all files (recursive)
     *
     * @param string $path
     * @param string $to
     * @param string|null $fileSystemName
     * @return void
     */
    public function moveFiles(string $from, string $to, ?string $fileSystemName = null)
    {
        $fileSystemName = $fileSystemName ?? Self::ROOT_FILESYSTEM_NAME;        
        $files = $this->listContents($from,true,$fileSystemName);

        foreach ($files as $item) {
            $this->copy($item['path'],$to,$fileSystemName);
            $this->delete($item['path'],$fileSystemName);                    
        }       
    }

    /**
     * Delete file from storage folder
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return boolean
     */
    public function delete(string $path, ?string $fileSystemName = null): bool
    {
        return (bool)$this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->delete($path);           
    }
    
    /**
     * Rename files
     *
     * @param string $from
     * @param string $to
     * @param string|null $fileSystemName
     * @return boolean
     */
    public function rename(string $from, string $to, ?string $fileSystemName = null): bool
    {
        return (bool)$this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->rename($from,$to);        
    }

    /**
     * Delete directory in storage folder
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return boolean
     */
    public function deleteDir(string $path, ?string $fileSystemName = null): bool
    {
        return (bool)$this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->deleteDir($path);      
    }

    /**
     * Create directory in storage folder
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return boolean
     */
    public function createDir(string $path, ?string $fileSystemName = null): bool
    {
        return (bool)$this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->createDir($path);                   
    }

    /**
     * Return true if file exist
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return boolean
     */
    public function has(string $path, ?string $fileSystemName = null): bool
    {
        try {
            return (bool)$this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->has($path);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Copy files
     *
     * @param string $from
     * @param string $to
     * @param string|null $fileSystemName
     * @return bool
     */
    public function copy(string $from, string $to, ?string $fileSystemName = null): bool
    {
        return (bool)$this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->copy($from,$to);       
    }

    /**
     * Move file
     *
     * @param string $from
     * @param string $to
     * @param string|null $fileSystemName
     * @return boolean
     */
    public function moveFile(string $from, string $to, ?string $fileSystemName = null): bool
    {
        $fileSystemName = $fileSystemName ?? Self::ROOT_FILESYSTEM_NAME;  

        $result = $this->copy($from,$to,$fileSystemName);  
        if ($result == false) {
            return false;
        }    
        
        return $this->delete($from,$fileSystemName);
    }

    /**
     * Get file info
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return array|false
     */
    public function getMetadata(string $path, ?string $fileSystemName = null)
    {
        try {
            $this->errorMessage = null;
            return $this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->getMetadata($path);
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    /**
     * Return true if file is directory
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return boolean
     */
    public function isDir(string $path, ?string $fileSystemName = null): bool
    {
        $fileSystemName = $fileSystemName ?? Self::ROOT_FILESYSTEM_NAME;  
        if ($this->has($path,$fileSystemName) == false) {
            return false;
        }

        $meta = $this->getMetadata($path,$fileSystemName);

        return (($meta['type'] ?? null) == 'dir');
    }

    /**
     * Get Mimetypes
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return string|false
     */
    public function getMimetype(string $path, ?string $fileSystemName = null)
    {
        try {
            $this->errorMessage = null;
            return $this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->getMimetype($path);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    /**
     * Get file size
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return integer|false
     */
    public function getSize(string $path, ?string $fileSystemName = null)
    {
        return $this->get($fileSystemName ?? Self::ROOT_FILESYSTEM_NAME)->getSize($path);
    }

    /**
     * Get Mount Manager
     *
     * @return MountManager
     */
    public function manager()
    {
        return $this->manager;
    }

    /**
     * Get sytem directories
     *
     * @return array
     */
    public function getSystemDirectories(): array
    {
        return $this->systemDir;
    }

    /**
     * Return true if path is system dir
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return boolean
    */
    public function isSystemDir(string $path, ?string $fileSystemName = null): bool
    {
        $fileSystemName = $fileSystemName ?? Self::ROOT_FILESYSTEM_NAME; 

        return ($fileSystemName == Self::ROOT_FILESYSTEM_NAME) ? \in_array($path,$this->systemDir) : false;
    }

    /**
     * Return true if filesystem is local
     *
     * @param string|null $name
     * @return boolean
    */
    public function isLocalFilesystem(?string $name): bool
    {
        return (empty($name) == true) ? true : \in_array($name,$this->localFilesystems);          
    }

    /**
     * Get root filesystem name
     *
     * @return string
     */
    public function getRootFilesystemName(): string
    {
        return Self::ROOT_FILESYSTEM_NAME;
    }

    /**
     * Get recycle bin path
     *
     * @return string
     */
    public function getRecyleBinPath(): string
    {
        return Self::RECYCLE_BIN_PATH;
    }

    /**
     * Get pubic path
     *
     * @param boolean $relative
     * @return string
     */
    public function getPublicPath(bool $relative = false): string
    {
        return ($relative == true) ? 'public' . DIRECTORY_SEPARATOR : Path::STORAGE_PUBLIC_PATH;
    } 
}
