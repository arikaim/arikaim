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
     * @param string $eventName
     * @param integer $priority
     */
    public function __construct($eventName = null, $priority = 0, $hadnlerMethod = null)
    {
        if ($eventName != null) {
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
    public function subscribe($eventName, $hadnlerMethod = null, $priority = 0)
    {
        $event = [
            'event_name'     => $eventName,
            'handler_method' => $hadnlerMethod,        
            'priority'       => $priority
        ];
        $this->subscribedEvents[] = $event;        
    }

    /**
     * Return subscribed events.
     *
     * @return array
     */
    public function getSubscribedEvents() 
    {
        return $this->subscribedEvents;
    }
}
