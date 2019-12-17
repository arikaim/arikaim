<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\Events;

/**
 * Event Registry Interface
 */
interface EventRegistryInterface 
{
    /**
     * Add event to events db table.
     *
     * @param string $name
     * @param string $title
     * @param string $extension
     * @param string $description
     * @return bool
     */
    public function registerEvent($name, $title, $extension = null, $description = null);

    /**
     * Return true if event exist
     *
     * @param string $name
     * @return boolean
     */
    public function hasEvent($name);

    /**
     * Deleet event
     *
     * @param string $name
     * @return bool
     */
    public function deleteEvent($name);

    /**
     * Delete events.
     *
     * @param array $filter
     * @return bool
     */
    public function deleteEvents(array $filter);

    /**
     * Get events list
     *
     * @param array $filter
     * @return array
     */
    public function getEvents(array $filter = []);

    /**
     * Set events status
     *
     * @param array $filter
     * @param integer $status
     * @return boolean
     */
    public function setEventsStatus(array $filter = [], $status);
}
