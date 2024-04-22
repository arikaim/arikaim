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
     * @param string|null $extension
     * @param string|null $description
     * @return bool
     */
    public function registerEvent(string $name, string $title, ?string $extension = null, ?string $description = null): bool;

    /**
     * Return true if event exist
     *
     * @param string $name
     * @return boolean
     */
    public function hasEvent(string $name): bool;

    /**
     * Deleet event
     *
     * @param string $name
     * @return bool
     */
    public function deleteEvent(string $name): bool;

    /**
     * Delete events.
     *
     * @param array $filter
     * @return bool
     */
    public function deleteEvents(array $filter): bool;

    /**
     * Get events list
     *
     * @param array $filter
     * @return array
     */
    public function getEvents(array $filter = []): array;

    /**
     * Set events status
     *
     * @param array $filter
     * @param integer $status
     * @return boolean
     */
    public function setEventsStatus(array $filter, int $status): bool;
}
