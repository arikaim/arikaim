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
    public function dispatch(string $eventName, $event = [], bool $callbackOnly = false, ?string $extension = null): array;

    /**
     * Register event subscriber.
     *
     * @param object|string $subscriber Subscriber class or object ref
     * @param string|null $extension
     * @return bool
     */
    public function registerSubscriber($subscriber, ?string $extension): bool;

    /**
     * Add event to events db table.
     *
     * @param string $name
     * @param string"null $title
     * @param string|null $extension
     * @param string|null $description
     * @return bool
     */
    public function registerEvent(string $name, ?string $title, ?string $extension = null, ?string $description = null): bool;

    /**
     * Subscribe callback
     *
     * @param string $eventName
     * @param Closure $callback
     * @param boolean $single
     * @return void
     */
    public function subscribeCallback(string $eventName, $callback, bool $single = false): void;
}
