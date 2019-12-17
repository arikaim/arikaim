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
 * Collection interface
 */
interface CollectionInterface
{    
    /**
     * Delete all collection items
     *
     * @return void
     */
    public function clear();

    /**
     * Copy collection 
     *
     * @return void
     */
    public function copy();

    /**
     * Return true if collection item is empty
     *
     * @param string $key
     * @return boolean
     */
    public function isEmpty($key);

    /**
     * Convert collection to array
     *
     * @return array
     */
    public function toArray();
}
