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
     * @var string|null
     */
    protected $eventName;

    /**
     * Event priority
     *
     * @var integer
     */
    protected $priority = 0;

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
     * @param string|null $eventName
     * @param integer $priority
     */
    public function __construct(?string $eventName = null, int $priority = 0)
    {
        $this->eventName = $eventName;
        $this->priority = $priority;
        
        if (empty($this->eventName) == false) {
            $this->subscribe($eventName,$priority);
        }
    }
    
    /**
     * Get event name
     *
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority(): int
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
    public function subscribe(string $eventName, int $priority = 0): void
    {
        $this->eventName = $eventName;
        $this->priority = $priority;
    }
}
