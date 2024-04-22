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
use Arikaim\Core\Content\ContentTypeRegistry;
use Arikaim\Core\Content\ContentSelector;
use Arikaim\Core\Content\ContentItem;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\System\Traits\PhpConfigFile;
use Arikaim\Core\Interfaces\Content\ContentManagerInterface;
use Arikaim\Core\Interfaces\Content\ContentProviderInterface;
use Arikaim\Core\Interfaces\Content\ContentItemInterface;
use Arikaim\Core\Utils\Uuid;
use Exception;

/**
 *  Content providers registry manager
 */
class ContentManager implements ContentManagerInterface
{
    use PhpConfigFile;

    /**
     *  Default providers config file name
     */
    const PROVIDERS_FILE_NAME = Path::CONFIG_PATH . 'content-providers.php';

    /**
     * Content providers
     *
     * @var array|null
     */
    protected $contentProviders = null;

    /**
     * Content type registry
     *
     * @var null
     */
    protected $contentTypeRegistry = null;

    /**
     * Content providers config file
     *
     * @var string
     */
    private $providersFileName;

    /**
     * Constructor
     * 
     * @param string|null $providersFileName
     */
    public function __construct(?string $providersFileName = null)
    {
        $this->providersFileName = $providersFileName ?? Self::PROVIDERS_FILE_NAME;                 
    }

    /**
     * Search content
     *
     * @param string $selector
     * @param string $query
     * @param integer $page
     * @param integer $perPage
     * @return mixed
     */
    public function search(string $selector, string $query = '', int $page = 1, int $perPage = 25)
    {
        $data = ContentSelector::parse($selector);
        $searchField = $data['key_fields'][0] ?? '';
        if (empty($searchField) == true) {
            return false;
        }
    
        $contentType = $this->typeRegistry()->get($searchField);
        if ($contentType != null) {
            $data['key_fields'] = $contentType->getSearchableFieldNames();
        }

        if ($data['type'] == ContentSelector::DB_MODEL_TYPE) {
            $model = \Arikaim\Core\Db\Model::create($data['provider'],$data['content_type'] ?? null);
            if ($model == null) {
                return false;
            }
            $model = $model->whereRaw('UPPER(' . $searchField . ') LIKE ?',['%' . $query . '%']);
            
            return $model->get();
        }

        $provider = $this->type($data['content_type'],$data['provider'] ?? null);
        if ($provider == null) {
            return false;
        }
        $data['query'] = $query;

        return $provider->getContentItems($data,$page,$perPage);
    }

    /**
     * Create content selector
     *
     * @param string $provider
     * @param string $contentType
     * @param string $keyFields
     * @param string $key
     * @param string $type
     * @return string
     */
    public function createSelector(
        string $provider,
        string $contentType,
        string $keyFields,
        string $key, 
        string $type = 'content'
    ): string
    {
        return ContentSelector::create($provider,$contentType,$keyFields,$key,$type);
    }

    /**
     * Save content item
     *
     * @param string      $key
     * @param string      $contentType
     * @param string|null $title
     * @return mixed
     */
    public function saveContentItem(string $key, string $contentType, ?string $title = null)
    {
        $provider = $this->getDefaultProvider($contentType);
        if ($provider == null) {
            return false;
        }

        list($contentId,$content) = $provider->createItem([
            'user_id' => null
        ]);

        return \Arikaim\Core\Db\Model::Content('content')->saveItem($key,$contentType,$contentId,$title);
    }

    /**
     * Get content item
     *
     * @param string $key
     * @return ContentItemInterface|null
     */
    public function getItem(string $key, ?array $default = null): ?ContentItemInterface
    {
        global $arikaim;

        $userId = $arikaim->get('access')->getId();
       
        $data = \Arikaim\Core\Db\Model::Content('content')->findByKey($key,$userId);
        if ($data == null) {
            // find public
            $data = \Arikaim\Core\Db\Model::Content('content')->findByKey($key,null);
        }

        if ($data == null) {
            return ($default == null) ? null : ContentItem::create($default,ArrayContentType::create(),$key);
        }

        if ($data->status != 1) {
            // disabled
            return ($default == null) ? null : ContentItem::create($default,ArrayContentType::create(),$key);
        }

        $provider = $this->getDefaultProvider($data->content_type);
        if ($provider == null) {
            return ($default == null) ? null : ContentItem::create($default,ArrayContentType::create(),$key);
        }
 
        $contentItem = $provider->get($data->content_id,$data->content_type);
        if ($contentItem == null) {
            return ($default == null) ? null : ContentItem::create($default,ArrayContentType::create(),$key);
        }

        if ($contentItem->isEmpty() == false) {
            return $contentItem;
        }

        return ($default == null) ? null : ContentItem::create($default,ArrayContentType::create(),$key);
    }

    /**
     * Get content
     *
     * @param string $selector
     * @return ContentItemInterface|null
     */
    public function get(string $selector): ?ContentItemInterface
    {
        $data = ContentSelector::parse($selector);
        if ($data == null) {
            return null;
        }

        if ($data['type'] == ContentSelector::DB_MODEL_TYPE) {
            $model = \Arikaim\Core\Db\Model::create($data['provider'],$data['content_type'] ?? null);
            if ($model == null) {
                return null;
            }

            foreach ($data['key_fields'] as $index => $key) {
                $value = $data['key_values'][$index] ?? null;
                if (empty($value) == false) {
                    $model = $model->where($key,'=',$value);
                }                
            }
            $model = $model->first();
            $data = ($model != null) ? $model->toArray() : [];

            return ContentItem::create($data,ArrayContentType::create(),(string)$data['id']);
        }

        $provider = $this->type($data['content_type'],$data['provider'] ?? null);
        if ($provider == null) {
            return null;
        }

        $contentItem = $provider->getContent($data['key_values'],$data['content_type']);
        if ($contentItem == null) {
            return null;
        }
        // resolve content Id
        $key = $data['key_fields'][0] ?? 'id';
        $id = $contentItem['uuid'] ?? $contentItem['id'] ?? $contentItem[$key] ?? '';
        $contentType = $this->typeRegistry()->get($data['content_type']);

        return ContentItem::create($contentItem,$contentType,(string)$id);
    }

    /**
     * Return true if content type exists
     *
     * @param string $name
     * @return boolean
     */
    public function hasContentType(string $name): bool
    {
        return $this->typeRegistry()->has($name);
    } 

    /**
     * Get content type provider from registry
     *
     * @param string $name
     * @return ContentProviderInterface|null
     */
    public function type(string $name, ?string $providerName = null): ?ContentProviderInterface
    {        
        $contentType = $this->typeRegistry()->get($name);
        if (empty($providerName) == true) {
            $providers = $this->typeRegistry()->getProviders($name);
            $providerName = $providers[0] ?? null;
        }      
        if (empty($providerName) == true || $contentType == null) {
            return null;
        }

        $provider = $this->provider($providerName);
        if ($provider == null) {
            return null;
        }

        $provider->setContentType($contentType);

        return $provider;
    }

    /**
     * Get content type registry
     *
     * @return object
     */
    public function typeRegistry()
    {
        if ($this->contentTypeRegistry == null) {
            $this->contentTypeRegistry = new ContentTypeRegistry();
        }

        return $this->contentTypeRegistry;
    }

    /**
     * Run action
     *
     * @param string     $contentType
     * @param string     $actionName
     * @param mixed     $data
     * @param array|null $options
     * @return mixed
     */
    public function runAction(string $contentType, string $actionName, $data, ?array $options = [])
    {
        $type = $this->typeRegistry()->get($contentType);
        $actions = $type->getActions();
        $action = $actions[$actionName] ?? null;

        if (\is_object($action) == false) {
            return false;
        }
        if (\is_object($data) == true) {
            $data = $data->toArray();
        }
        if (\is_array($data) == false) {
            $data = [$data];
        }

        return $action->execute($data,$options);
    }

    /**
     * Create content item
     *
     * @param mixed $data
     * @param string $contentType
     * @return mixed
     */
    public function createItem($data, string $contentType)
    {
        $type = $this->typeRegistry()->get($contentType);
          
        if (\is_object($data) == true) {
            $data = $data->toArray();
        }
        if (\is_array($data) == false) {
            $data = [$data];
        }

        // resolve content Id
        $id = $data['uuid'] ?? $data['id'] ?? Uuid::create();

        return ContentItem::create($data,$type,(string)$id);
    }

    /**
     * Load content providers and content types
     *
     * @param boolean $reload
     * @return void
     */
    public function load(bool $reload = false): void
    {
        if (($this->contentProviders === null) || ($reload == true)) {
            $this->contentProviders = $this->include($this->providersFileName);
        }        
    }

    /**
     * Get content providers list
     *
     * @param string|null $category
     * @param string|null $contentType
     * @return array
     */
    public function getProviders(?string $category = null, ?string $contentType = null): array
    {
        $this->load();     
        if ((empty($category) == true) && (empty($contentType) == true)) {
            return $this->contentProviders ?? [];
        }
        
        $result = [];
        foreach ($this->contentProviders as $item) {
            if ($item['category'] == $category || empty($category) == true) {
                if (empty($contentType) == true) {
                    $result[] = $item;
                } elseif (\in_array($contentType,$item['type']) == true) {
                    $result[] = $item;
                }
            }
        }
        
        return $result;      
    }

    /**
     * Get default provider
     *
     * @param string $contentType
     * @return object|null
     */
    public function getDefaultProvider(string $contentType): ?object
    {
        $providers = $this->typeRegistry()->getProviders($contentType);
        
        if (isset($providers[0]) == false) {
            return null;
        }

        return $this->provider($providers[0],$contentType);
    }

    /**
     * Get content provider
     * 
     * @param string $name
     * @param string|null $contentType
     * @return ContentProviderInterface|null
     */
    public function provider(string $name, ?string $contentType = null): ?ContentProviderInterface
    {
        $this->load();     
        $item = $this->contentProviders[$name] ?? null;
        if (empty($item) == true) {
            return null;
        }

        $provider = new $item['handler']();
        
        // resolve content type
        if (empty($contentType) == false) {
            $type = $this->typeRegistry()->get($contentType);         
            $provider->setContentType($type);
        }

        return $provider;
    }

    /**
     * Check if provider exists
     *
     * @param string $name
     * @return boolean
     */
    public function hasProvider(string $name): bool
    {
        return !empty($this->provider($name));
    } 

    /**
     * Register content provider
     *
     * @param object|string $provider
     * @return boolean
     */
    public function registerProvider($provider): bool
    { 
        if (($provider instanceof ContentProviderInterface) == false) {
            throw new Exception('Not valid content provider class.');
            return false;
        }

        $details = $this->resolveProviderDetails($provider);

        // load current array
        $this->contentProviders = $this->includePhpArray($this->providersFileName);        
        // add content provider
        $this->contentProviders[$details['name']] = $details;

        // register provider in content type
        $supportedContentTypes = $details['type'] ?? [];
        foreach($supportedContentTypes as $type) {
            $this->typeRegistry()->addProvider($type,$details['name']);
        }

        return $this->saveConfigFile($this->providersFileName,$this->contentProviders);    
    }

    /**
     * Unregister content provider
     *
     * @param string $name
     * @return boolean
     */
    public function unRegisterProvider(string $name): bool
    {
        $provider = $this->provider($name);
        if ($provider == null) {
            return true;   
        }
        $name = $provider->getProviderName();

        unset($this->contentProviders[$name]);

        return $this->saveConfigFile($this->providersFileName,$this->contentProviders);     
    }

    /**
     * Resolve provider edetails
     *
     * @param ContentProviderInterface $details
     * @throws Exception
     * @return array
     */
    protected function resolveProviderDetails(ContentProviderInterface $provider): array
    {
        return [
            'handler'  => \get_class($provider),
            'name'     => $provider->getProviderName(),
            'title'    => $provider->getProviderTitle(),
            'type'     => $provider->getSupportedContentTypes(),
            'category' => $provider->getProviderCategory()
        ];       
    }
}
