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
class FeedCollection extends Collection implements CollectionInterface, FeedsInterface, \Countable, \ArrayAccess, \IteratorAggregate
{
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
     * Constructor
     *
     *  @param string|null $baseUrl
     *  @param array|string $params
     *  @param string|null $itemsKey
     *  @param string|null $pageKey
     *  @param string|null $perPageKey
     */
    public function __construct($baseUrl = null, $params = [], $itemsKey = null, $pageKey = null, $perPageKey = null) 
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
     * @param integer $id;
     * @param integer|null $page;
     * @param integer|null $perPage;
     * @return array|null
     */
    public function findItem($key, $value, $page = null, $perPage = null)
    {
        $this->fetch($page,$perPage);
        $items = $this->getItems();
       
        foreach ($items as $item) {
            if (isset($item[$key]) == true) {
                if ($item[$key] == $value) {
                    return $item;
                }
            }
        }        

        return null;
    }

    /**
     * Set page array key for params array 
     *
     * @param string $key
     * @return FeedCollection
     */
    public function pageKey($key)
    {
        $this->pageKey = $key;
        return $this;
    }

    /**
     * Get pake key 
     *
     * @return integer
     */
    public function getPageKey()
    {
        return $this->pageKey;
    }

    /**
     * Set per page array key for params array 
     *
     * @param string $key
     * @return FeedCollection
     */
    public function perPageKey($key)
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
    public function baseUrl($url)
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
    public function itemsKey($key)
    {
        $this->itemsKey = $key;
        return $this;
    }

    /**
     * Fetch feed
     *
     * @return FeedCollection
     */
    public function fetch($page = null, $perPage = null)
    {
        $this->setPage($page);
        $this->setPerPage($perPage);

        $url = $this->getUrl();
        $json = Curl::get($url);
        $data = json_decode($json,true);
        if (is_array($data) == true) {
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
    public function setPage($page)
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
    public function setPerPage($perPage)
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
    public function getUrl()
    {
        $queryString = (is_string($this->params) == true) ? $this->params : '';

        if (is_array($this->params) == true) {
            if (Arrays::isAssociative($this->params) == true) {
                $queryString = "?" . http_build_query($this->params);
            } else {              
                foreach ($this->params as $value) {
                    $queryString .= $value . "/";
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
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Get items key
     *
     * @return string|null
     */
    public function getItemsKey()
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
     * @return array|null
     */
    public function getItems($keyMaps = true)
    {
        $items = $this->getItemsArray();
        return ($keyMaps == true) ? $this->applyKeyMaps($items) : $items;
    }

    /**
     * Get items array
     *
     * @return array
     */
    protected function getItemsArray()
    {
        if (empty($this->itemsKey) == true) {
            $items = $this->data;
        } else {
            $items = (isset($this->data[$this->itemsKey]) == true) ? $this->data[$this->itemsKey] : null;
        }
        return $items;
    } 

    /**
     * Get feed item
     *
     * @param integer $index
     * @param boolean $keyMaps
     * @return mixed
     */
    public function getItem($index, $keyMaps = true)
    {
        $items = $this->getItemsArray();
        $item = (isset($items[$index]) == true) ? $items[$index] : [];
        
        return ($keyMaps == true) ? $this->transformItem($item) : $item;           
    }

    /**
     * Set key maps
     *
     * @param array $keyMaps
     * @return void
     */
    public function setKeyMaps($keyMaps)
    {
        $this->keyMaps = $keyMaps;
    }

    /**
     * Change array key 
     *
     * @param string $key
     * @param string $mapTo
     * @return FeedCollection
     */
    public function mapKey($key, $mapTo)
    {
        $this->keyMaps[$key] = $mapTo;
        return $this;
    }

    /**
     * Change item array keys
     *
     * @param array $items
     * @return array
     */
    public function applyKeyMaps($items = null)
    {
        $items = (empty($items) == true) ? $this->data : $items;
 
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
    protected function transformItem($item)
    {
        foreach ($this->keyMaps as $key => $value) {
            if (is_callable($value) == true) {          
                $callback = function() use($value,$item) {
                    return $value($item);
                };        
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
