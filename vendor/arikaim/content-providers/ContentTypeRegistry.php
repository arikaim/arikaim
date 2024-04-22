<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Content;

use Arikaim\Core\Content\Type\ArrayContentType;
use Arikaim\Core\Interfaces\Content\ContentTypeInterface;
use Arikaim\Core\Interfaces\Content\ActionInterface;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\System\Traits\PhpConfigFile;
use Exception;

/**
 *  Content type registry manager
 */
class ContentTypeRegistry 
{
    use PhpConfigFile;

    /**
     *  Default content types registry config file name
    */
    const CONTENT_TYPES_FILE_NAME = Path::CONFIG_PATH . 'content-types.php';

    /**
     * Content types
     *
     * @var array|null
     */
    protected $contentTypes = null;

    /**
     * Content types config file
     *
     * @var string
     */
    private $contentTypesFileName;

    /**
     * Constructor
     * 
     * @param string|null $providersFileName
     */
    public function __construct(?string $contentTypesFileName = null)
    {
        $this->contentTypesFileName = $contentTypesFileName ?? Self::CONTENT_TYPES_FILE_NAME;         
    }

    /**
     * Load content providers and content types
     *
     * @param boolean $reload
     * @return void
     */
    public function load(bool $reload = false): void
    {
        if (($this->contentTypes === null) || ($reload == true)) {
            $this->contentTypes = $this->include($this->contentTypesFileName);
        }
    }

    /**
     * Get registered content types list
     *
     * @param string|null $category
     * @return array
     */
    public function getContentTypes(?string $category = null): array
    {
        $this->load();     
        if ((empty($category) == true) && (empty($contentType) == true)) {
            return $this->contentTypes ?? [];
        }
        
        $result = [];
        foreach ($this->contentTypes as $item) {
            if ($item['category'] == $category) {
                $result[] = $item;
            }
        }
        
        return $result;      
    }

    /**
     * Get content type
     * 
     * @param string $name
     * @return ContentTypeInterface
     */
    public function get(string $name): ContentTypeInterface
    {
        $this->load();     
        $item = $this->contentTypes[$name] ?? null;
        if (empty($item) == true) {
            return new ArrayContentType();          
        }

        $contentType = new $item['handler']();
        $contentType->setActionHandlers($item['actions'] ?? []);

        return $contentType;
    }

    /**
     * Check if content type exists
     *
     * @param string $name
     * @return boolean
     */
    public function has(string $name): bool
    {
        $this->load();     
        
        return !empty($this->contentTypes[$name] ?? null);
    } 

    /**
     * Get all content provider for type
     *
     * @param string $contentType
     * @return array|null
     */
    public function getProviders(string $contentType): ?array
    {
        $this->load();    
        $item = $this->contentTypes[$contentType] ?? null;

        return (empty($item) == true) ? null : $item['providers'] ?? null; 
    }

    /**
     * Add content type provider
     *
     * @param string $contentType
     * @param string $name
     * @return boolean
     */
    public function addProvider(string $contentType, string $name): bool
    {
        $this->load();    
        $item = $this->contentTypes[$contentType] ?? null;
        if (empty($item) == true) {
            return false;
        }
        $providers = $item['providers'] ?? [];
        $providers[] = $name;
        $providers = \array_unique($providers);

        $this->contentTypes[$contentType]['providers'] = $providers;

        return $this->saveConfigFile($this->contentTypesFileName,$this->contentTypes);   
    }

    /**
     * Register content type action
     *
     * @param string $contentType
     * @param string $class
     * @throws Exception
     * @return bool
     */
    public function registerAction(string $contentType, string $class): bool
    {
        $this->load();  
        $item = $this->contentTypes[$contentType] ?? null;
        if (empty($item) == true) {
            throw new Exception('Not valid content type ' . $contentType,1);            
            return false;
        }  

        if (\class_exists($class) == false) {
            throw new Exception('Not valid content type action ' . $class,1);       
            return false;
        }
        $action = new $class();
        
        if (($action instanceof ActionInterface) == false) {
            throw new Exception('Not valid content type action ' . $class,1);       
            return false;
        }

        $actions = $item['actions'] ?? [];
        $actions[] = $class;
        $actions = \array_unique($actions);

        $this->contentTypes[$contentType]['actions'] = $actions;

        return $this->saveConfigFile($this->contentTypesFileName,$this->contentTypes);   
    }

    /**
     * Register content provider
     *
     * @param object|string $provider
     * @throws Exception
     * @return boolean
     */
    public function register($contentType): bool
    { 
        if (($contentType instanceof ContentTypeInterface) == false) {
            throw new Exception('Not valid content type class.');
            return false;
        }

        $details = $this->resolveDetails($contentType);
        $name = $contentType->getName();
        
        // unregister
        $this->unRegister($name);

        // load current array
        $this->contentTypes = $this->includePhpArray($this->contentTypesFileName);        

        if (isset($this->contentTypes[$name]) == true) {
            // update
            $actions = \array_merge($details['actions'],$this->contentTypes[$name]['actions'] ?? []);
            $details['actions'] = \array_unique($actions);                   
        }
        // add content provider
        $this->contentTypes[$name] = $details;
        
        // save
        return $this->saveConfigFile($this->contentTypesFileName,$this->contentTypes);    
    }

    /**
     * Unregister content provider
     *
     * @param string $name
     * @return boolean
     */
    public function unRegister(string $name): bool
    {
        $contentType = $this->get($name);
        if ($contentType == null) {
            return true;   
        }
        $name = $contentType->getName();

        unset($this->contentTypes[$name]);

        return $this->saveConfigFile($this->contentTypesFileName,$this->contentTypes);     
    }

    /**
     * Resolve content type details
     *
     * @param ContentTypeInterface $contentType
     * @throws Exception
     * @return array
     */
    protected function resolveDetails(ContentTypeInterface $contentType): array
    {
        return [
            'handler'  => \get_class($contentType),
            'name'     => $contentType->getName(),
            'title'    => $contentType->getTitle(),
            'category' => $contentType->getCategory(),
            'actions'  => $contentType->getActionHandlers()
        ];       
    }
}
