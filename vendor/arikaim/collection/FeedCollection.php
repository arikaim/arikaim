<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Collection;

use Arikaim\Core\Collection\Interfaces\CollectionInterface;
use Arikaim\Core\Collection\Interfaces\FeedsInterface;
use Arikaim\Core\Collection\Arrays;
use Arikaim\Core\Utils\Curl;

/**
 * Feed Collection class
 */
class FeedCollection extends Collection implements 
    CollectionInterface, 
    FeedsInterface, 
    \Countable,
    \ArrayAccess, 
    \IteratorAggregate
{

    const DEFAULT_ITEMS_PER_PAGE = 25;

    /**
     * Item key mappings
     *
     * @var array
     */
    protected $keyMaps = [];

    /**
     * Feed base url
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Url params
     *
     * @var array|string
     */
    protected $params;

    /**
     * Feed items key
     *
     * @var string|null
     */
    protected $itemsKey;

    /**
     * Array key in params 
     *
     * @var string
     */
    protected $pageKey;

    /**
     * Items per page array key
     *
     * @var string|null
     */
    protected $perPageKey;

    /**
     *  Constructor
     *
     *  @param string|null $baseUrl
     *  @param array|string $params
     *  @param string|null $itemsKey
     *  @param string|null $pageKey
     *  @param string|null $perPageKey
     */
    public function __construct(
        ?string $baseUrl = null, 
        $params = [], 
        ?string $itemsKey = null, 
        ?string $pageKey = null, 
        ?string $perPageKey = null
    ) 
    {  
        $this->baseUrl = $baseUrl;
        $this->params = $params;
        $this->itemsKey = $itemsKey;
        $this->pageKey = $pageKey;
        $this->perPageKey = $perPageKey;

        parent::__construct([]);
    }

    /**
     * Find feed item
     *
     * @param string $key;
     * @param mixed $value;
     * @param integer|null $page;
     * @param integer|null $perPage;
     * @return array|null
     */
    public function findItem(string $key, $value, ?int $page = null, ?int $perPage = null): ?array
    {
        $value = \trim($value);

        $this->fetch($page,$perPage);
        $items = $this->getItems();
    
        foreach ($items as $item) {            
            if (isset($item[$key]) == true) {
                $text = \trim($item[$key]);
                if ($text == $value) {                   
                    return $item;
                }
            }
        }        

        return null;
    }

    /**
     * Set page array key for params array 
     *
     * @param string|null $key
     * @return FeedCollection
     */
    public function pageKey(?string $key)
    {
        $this->pageKey = $key;
        
        return $this;
    }

    /**
     * Get pake key 
     *
     * @return string|null
     */
    public function getPageKey(): ?string
    {
        return $this->pageKey;
    }

    /**
     * Set per page array key for params array 
     *
     * @param string|null $key
     * @return FeedCollection
     */
    public function perPageKey(?string $key)
    {
        $this->perPageKey = $key;

        return $this;
    }

    /**
     * Set feed base url
     *
     * @param string $url
     * @return FeedCollection
     */
    public function baseUrl(string $url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    /**
     * Set params
     *
     * @param array|string $params
     * @return FeedCollection
     */
    public function params($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Set items key
     *
     * @param string|null $key
     * @return FeedCollection
     */
    public function itemsKey(?string $key)
    {
        $this->itemsKey = $key;

        return $this;
    }

    /**
     * Fetch feed
     * 
     * @param int|null $page
     * @param int|null $perPage
     * @return FeedCollection
     */
    public function fetch(?int $page = null, ?int $perPage = Self::DEFAULT_ITEMS_PER_PAGE)
    {
        $page = $page ?? 1;
        $perPage = $perPage ?? Self::DEFAULT_ITEMS_PER_PAGE;

        $this->setPage($page);
        $this->setPerPage($perPage);

        $url = $this->getUrl();

        $json = Curl::get($url);
    
        $data = \json_decode($json,true);
        if (\is_array($data) == true) {
            $this->data = $data;
        }      

        return $this;
    }

    /**
     * Set feed current page
     *
     * @param integer $page
     * @return void
     */
    public function setPage(int $page): void
    {
        if (empty($this->pageKey) == false) {
            $this->params[$this->pageKey] = $page;
        }       
    }

    /**
     * Set feed items per page
     *
     * @param integer $perPage
     * @return void
     */
    public function setPerPage(int $perPage): void
    {
        if (empty($this->perPageKey) == false) {
            $this->params[$this->perPageKey] = $perPage;
        }       
    }

    /**
     * Get full url
     *
     * @return string
     */
    public function getUrl(): string
    {
        $queryString = (\is_string($this->params) == true) ? $this->params : '';

        if (\is_array($this->params) == true) {
            if (Arrays::isAssociative($this->params) == true) {
                $queryString = '?' . \http_build_query($this->params);
            } else {              
                foreach ($this->params as $value) {
                    $queryString .= $value . '/';
                }
            }           
        }     

        return $this->baseUrl . $queryString;
    }

    /**
     * Get base url
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get items key
     *
     * @return string|null
     */
    public function getItemsKey(): ?string
    {
        return $this->itemsKey;
    }

    /**
     * Get url params
     *
     * @return array|string
     */
    public function getUrlParams()
    {   
        return $this->params;
    }

    /**
     * Return feed items array
     *
     * @param boolean $keyMaps
     * @return array
     */
    public function getItems(bool $keyMaps = true): array
    {
        $items = $this->getItemsArray();

        return ($keyMaps == true) ? $this->applyKeyMaps($items) : $items;
    }

    /**
     * Get items array
     *
     * @return array|null
     */
    protected function getItemsArray(): ?array
    {
        if (empty($this->itemsKey) == true) {
            return $this->data;
        } 

        return $this->data[$this->itemsKey] ?? null;              
    } 

    /**
     * Get feed item
     *
     * @param integer $index
     * @param boolean $keyMaps
     * @return mixed
     */
    public function getItem($index, bool $keyMaps = true)
    {
        $items = $this->getItemsArray();
        $item = $items[$index] ?? [];
        
        return ($keyMaps == true) ? $this->transformItem($item) : $item;           
    }

    /**
     * Set key maps
     *
     * @param array $keyMaps
     * @return void
     */
    public function setKeyMaps(array $keyMaps): void
    {
        $this->keyMaps = $keyMaps;
    }

    /**
     * Change array key 
     *
     * @param string $key
     * @param mixed $mapTo
     * @return FeedCollection
     */
    public function mapKey(string $key, $mapTo)
    {
        $this->keyMaps[$key] = $mapTo;

        return $this;
    }

    /**
     * Change item array keys
     *
     * @param array|null $items
     * @return array
     */
    public function applyKeyMaps(?array $items = null): array
    {
        $items = $items ?? $this->data;
 
        foreach ($items as $key => $item) {                    
            $items[$key] = $this->transformItem($item);                       
        }

        return $items;
    }

    /**
     * Transform item
     *
     * @param array $item
     * @return array
     */
    protected function transformItem(array $item): array
    {
        foreach ($this->keyMaps as $key => $value) {
            if (\is_callable($value) == true) {                                
                $item[$key] = $value($item);
                continue;
            }
            if (isset($item[$value]) == true) {
                $item[$key] = $item[$value];
                unset($item[$value]);   
                continue;
            }
        }
        
        return $item;      
    }
}
