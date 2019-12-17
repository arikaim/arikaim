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
 * Subscriber Registry Interface
 */
interface SubscriberRegistryInterface 
{
    /**
     * Save subscriber info to db table. 
     *
     * @param string $eventName
     * @param string $class
     * @param string|null $extension
     * @param integer $priority
     * @return bool
     */
    public function addSubscriber($eventName, $class, $extension = null, $priority = 0, $hadnlerMethod = null);

    /**
     * Get subscribers list
     *
     * @param array $filter
     * @return array
     */
    public function getSubscribers(array $filter = []);

    /**
     * Delete subscribers.
     *
     * @param array $filter
     * @return bool
     */
    public function deleteSubscribers(array $filter);
}
