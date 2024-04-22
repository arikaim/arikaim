<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Packages\Traits;

/**
 * View components trait
*/
trait ViewComponents 
{
    /**
     * View path
     *
     * @var string|null
     */
    protected $viewPath;

    /**
     * Get view path
     *    
     * @param string|null $componentsType
     * @return string
     */
    public function getViewPath(?string $componentsType = null): string
    {
        switch ($this->getType()) {
            case 'template': 
                $path = $this->getPath() . $this->getName() . DIRECTORY_SEPARATOR;
                break;
            case 'components':
                $path = $this->getPath() . $this->getName() . DIRECTORY_SEPARATOR;
                break;
            default: 
                $path = (empty($this->viewPath) == true) ? $this->getPath() . $this->getName() . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR : $this->viewPath;
                break;
        }

        return (empty($componentsType) == true) ? $path : $path . $componentsType . DIRECTORY_SEPARATOR;
    }

    /**
     * Get components path
     *  
     * @return string
     */
    public function getComponentsPath(): string  
    {
        return $this->getViewPath('components');
    }

    /**
     * Get pages path
     *        
     * @return string
     */
    public function getPagesPath(): string  
    {
        return $this->getViewPath('pages');
    }

    /**
     * Get emails components path
     *
     * @return string
     */
    public function getEmailsPath(): string  
    {
        return $this->getViewPath('emails');
    }

    /**
     * Get macros path
     *
     * @return string
     */
    public function getMacrosPath(): string
    {
        return $this->getViewPath('macros');
    }

    /**
     * Scan directory and return macros list
     *
     * @param string|null $path
     * @return array
     */
    public function getMacros(?string $path = null): array
    {       
        $path = (empty($path) == true) ? $this->getMacrosPath() : $path;
        if (\file_exists($path) == false) {
            return [];
        }
        
        $items = [];
        foreach (new \DirectoryIterator($path) as $file) {
            if ($file->isDot() == true || $file->isDir() == true) continue;
            
            $fileExt = $file->getExtension();
            if ($fileExt != 'html' && $fileExt != 'htm') continue;           
            
            $item['name'] = \str_replace('.' . $fileExt,'',$file->getFilename());
            $items[] = $item;            
        }

        return $items;
    }

    /**
     * Scan directory and return pages list
     *
     * @param string|null $path
     * @return array
     */
    public function getPages(?string $parent = null): array
    {
        return $this->getComponents($parent,'pages');
    }

    /**
     * Scan directory and return emails list
     *
     * @param string|null $path
     * @return array
     */
    public function getEmails(?string $parent = null): array
    {
        return $this->getComponents($parent,'emails');
    }

    /**
     * Get component path
     *
     * @param string|null $componentName
     * @param string $type
     * @return string
     */
    public function getComponentPath(?string $componentName, string $type = 'components'): string
    {
        $componentName = $componentName ?? '';
        $componentPath = \str_replace('.',DIRECTORY_SEPARATOR,$componentName);
        
        return $this->getViewPath($type) . $componentPath;      
    }

    /**
     * Scan directory and return components list
     *
     * @param string|null $parent
     * @param string $type
     * @return array
     */
    public function getComponents(?string $parent = null, string $type = 'components'): array
    {
        $parent = $parent ?? '';
        $path = $this->getComponentPath($parent,$type);
        if (\file_exists($path) == false) {
            return [];
        }        

        $items = [];    
        foreach (new \DirectoryIterator($path) as $file) {
            if ($file->isDot() == true) continue;
            if ($file->isDir() == true) {
                $item['name'] = $file->getFilename();    
                if (\substr($item['name'],0,1) == '.') continue;
                
                $item['parent'] = $parent;                 
                $item['full_name'] = (empty($parent) == false) ? $item['parent'] . '.' . $item['name'] : $item['name'];  
                $fileId = (empty($parent) == false) ? $item['parent'] . '_' . $item['name'] : $item['name'];  
                $item['id'] = \str_replace('.','_',$fileId);

                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * Scan directory and return components list
     *
     * @param string|null $path
     * @return array
     */
    public function getComponentsRecursive(?string $path = null): array
    {       
        $path = (empty($path) == true) ? $this->getComponentsPath() : $path;
        if (\file_exists($path) == false) {
            return [];
        }        

        $items = [];
        $exclude = ['.git','.github'];
        $filter = function ($file, $key, $iterator) use ($exclude) {
            if ($iterator->hasChildren() && \in_array($file->getBaseName(),$exclude) == false) {
                return true;
            }
        
            return ($file->isDir() == true && \in_array($file->getBaseName(),$exclude) == false);
        };
        $dir = new \RecursiveDirectoryIterator($path,\RecursiveDirectoryIterator::SKIP_DOTS);    
        $filterIterator = new \RecursiveCallbackFilterIterator($dir,$filter);
        $iterator = new \RecursiveIteratorIterator($filterIterator,\RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $file) {
            $item['name'] = $file->getFilename();   
            $item['path'] = $file->getPathname();
            
            $componentPath = \str_replace($path,'',$file->getRealPath());                
            $componentPath = \str_replace(DIRECTORY_SEPARATOR,'.',$componentPath);
            
            $item['full_name'] = $componentPath;
            $items[] = $item;         
        }

        return $items;
    }
}
