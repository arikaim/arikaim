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
 * Event Dispatcher Interface
 */
interface EventDispatcherInterface 
{
    /**
     * Fire event, dispatch event data to all subscribers
     *
     * @param string $eventName
     * @param array|EventInterface $event
     * @param boolean $callbackOnly
     * @param string|null $extension
     * @return array
     */
    public function dispatch($eventName, $event = [], $callbackOnly = false, $extension = null);

    /**
     * Register event subscriber.
     *
     * @param string $class
     * @param string $extension
     * @return bool
     */
    public function registerSubscriber($class, $extension);

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
     * Subscribe callback
     *
     * @param string $eventName
     * @param Closure $callback
     * @param boolean $single
     * @return void
     */
    public function subscribeCallback($eventName, $callback, $single = false);
}
