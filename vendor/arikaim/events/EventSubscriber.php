<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Events;

use Arikaim\Core\Interfaces\Events\EventSubscriberInterface;

/**
 * Base class for event subscribers.
*/
class EventSubscriber implements EventSubscriberInterface
{
    /**
     * Events subscribed
     *
     * @var array
     */
    protected $subscribedEvents = [];

    /**
     * Constructor
     *
     * @param string|null $eventName
     * @param integer $priority
     * @param string|null $hadnlerMethod
     */
    public function __construct(?string $eventName = null, int $priority = 0, ?string $hadnlerMethod = null)
    {
        if (empty($eventName) == false) {
            $this->subscribe($eventName,$hadnlerMethod,$priority);
        }
    }
    
    /**
     * Subscriber code executed.
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function execute($event)
    {
    }

    /**
     * Subscribe to event.
     *
     * @param string $eventName    
     * @param string|null $hadnlerMethod    
     * @param integer $priority
     * @return void
     */
    public function subscribe(string $eventName, ?string $hadnlerMethod = null, int $priority = 0): void
    {
        $this->subscribedEvents[] = [
            'event_name'     => $eventName,
            'handler_method' => $hadnlerMethod,        
            'priority'       => $priority
        ];        
    }

    /**
     * Return subscribed events.
     *
     * @return array
     */
    public function getSubscribedEvents(): array 
    {
        return $this->subscribedEvents;
    }
}
