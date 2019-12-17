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

use Arikaim\Core\Interfaces\Events\EventListenerInterface;

/**
 * Base class for event listener.
*/
abstract class EventListener implements EventListenerInterface
{
    /**
     * Event name
     *
     * @var string
     */
    protected $eventName;

    /**
     * Event priority
     *
     * @var integer
     */
    protected $priority;

    /**
     * Subscriber code executed.
     *
     * @param EventInterface $event
     * @return mixed
     */
    abstract public function execute($event);

    /**
     * Constructor
     *
     * @param string $eventName
     * @param integer $priority
     */
    public function __construct($eventName = null, $priority = 0)
    {
        if ($eventName != null) {
            $this->subscribe($eventName,$priority);
        }
    }
    
    /**
     * Get event name
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Subscribe to event
     *
     * @param string $eventName
     * @param integer $priority
     * @return void
     */
    public function subscribe($eventName, $priority = 0)
    {
        $this->eventName = $eventName;
        $this->priority = $priority;
    }
}
