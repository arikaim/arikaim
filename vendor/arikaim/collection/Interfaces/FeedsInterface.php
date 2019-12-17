<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Collection\Interfaces;

/**
 * Feeds Collection interface
 */
interface FeedsInterface
{    
    /**
     * Fetch feed
     *
     * @param integer|null $page 
     * @param integer|null $perPage
     * @return boolean
     */
    public function fetch($page = null, $perPage = null);

    /**
     * Get feed item
     *
     * @param integer $index
     * @return mixed
     */
    public function getItem($index);

    /**
     * Return feed items array
     *
     * @return array|null
     */
    public function getItems();

    /**
     * Get items key
     *
     * @return string|null
     */
    public function getItemsKey();

    /**
     * Get base url
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * Get full url
     *
     * @return string
     */
    public function getUrl();
}
