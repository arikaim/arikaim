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

use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Events\Event;
use Arikaim\Core\Interfaces\Events\EventInterface;
use Arikaim\Core\Interfaces\Events\EventListenerInterface;
use Arikaim\Core\Interfaces\Events\EventSubscriberInterface;
use Arikaim\Core\Interfaces\Events\EventDispatcherInterface;
use Arikaim\Core\Interfaces\Events\EventRegistryInterface;
use Arikaim\Core\Interfaces\Events\SubscriberRegistryInterface;
use Arikaim\Core\Interfaces\Events\EventLogInterface;
use Arikaim\Core\Interfaces\LoggerInterface;
use Exception;

/**
 * Dispatch and manage events and event subscribers.
*/
class EventsManager implements EventDispatcherInterface
{
    /**
     * Subscribers
     *
     * @var array
     */
    protected $subscribers;

    /**
     * Event Registry
     *
     * @var EventRegistryInterface
     */
    protected $eventRegistry;

    /**
     * Subscriber Registry
     *
     * @var SubscriberRegistryInterface
     */
    protected $subscriberRegistry;

    /**
     * Logger ref
     *
     * @var LoggerInterface|null
     */
    private $logger = null;

    /**
     * Options
     *
     * @var array
     */
    private $options = [];

    /**
     * Constructor
     */
    public function __construct(
        EventRegistryInterface $eventRegistry, 
        SubscriberRegistryInterface $subscriberRegistry,
        ?LoggerInterface $logger = null,
        array $options = []
    )
    {
        $this->subscribers = [];
        $this->eventRegistry = $eventRegistry;
        $this->subscriberRegistry = $subscriberRegistry;
        $this->logger = $logger;
        $this->options = $options;
    }
    
    /**
     * Set events status
     *
     * @param array $filter
     * @param integer $status
     * @return boolean
     */
    public function setEventsStatus(array $filter, int $status): bool
    {
        return $this->eventRegistry->setEventsStatus($filter,$status);
    }

    /**
     * Return true if event exist
     *
     * @param string $name
     * @return boolean
     */
    public function hasEvent(string $name): bool
    {
        return $this->eventRegistry->hasEvent($name);
    }

    /**
     * Delete events.
     *
     * @param array $filter
     * @return bool
     */
    public function deleteEvents(array $filter): bool
    {
        return $this->eventRegistry->deleteEvents($filter);
    }

    /**
     * Delete subscribers.
     *
     * @param array $filter
     * @return bool
     */
    public function deleteSubscribers(array $filter): bool
    {
        return $this->subscriberRegistry->deleteSubscribers($filter);
    }

    /**
     * Get events list
     *
     * @param array $filter
     * @return array
     */
    public function getEvents(array $filter = []): array
    {
        return $this->eventRegistry->getEvents($filter);
    }

    /**
     * Get subscribers list
     *
     * @param string|null $eventName
     * @param string|null $extensionName
     * @param integer|null $status
     * @return array
     */
    public function getSubscribers(?string $eventName = null, ?string $extensionName = null, ?int $status = null): array
    {
        return $this->subscriberRegistry->getSubscribers($eventName,$extensionName,$status);
    }

    /**
     * Unregister event
     *
     * @param string $eventName
     * @return bool
     */
    public function unregisterEvent(string $eventName): bool
    {
        return $this->eventRegistry->deleteEvent($eventName);
    }

    /**
     * Add event to events db table.
     *
     * @param string $name
     * @param string"null $title
     * @param string|null $extension
     * @param string|null $description
     * @return bool
     */
    public function registerEvent(string $name, ?string $title, ?string $extension = null, ?string $description = null): bool
    {
        if (($this->isCoreEvent($name) == true) && ($extension != null)) {
            // core events can't be registered from extension
            return false;
        }
        
        return $this->eventRegistry->registerEvent($name,$title,$extension,$description);
    }

    /**
     * Save event properties descrition
     *
     * @param string $name
     * @param mixed $descriptor
     * @return boolean
     */
    public function savePropertiesDescriptor(string $name, $descriptor): bool
    {
        return $this->eventRegistry->savePropertiesDescriptor($name,$descriptor);
    }

    /**
     * Check if event name is core event name.
     *
     * @param string $name
     * @return boolean
     */
    public function isCoreEvent(string $name): bool
    {
        return (\substr($name,0,4) == 'core');      
    }

    /**
     * Register event subscriber.
     *
     * @param string|object $subscriber Subscriber class or object ref
     * @param string|null $extension
     * @return bool
     */
    public function registerSubscriber($subscriber, ?string $extension): bool
    {
        if (\is_object($subscriber) == false) {
            $subscriber = Factory::createEventSubscriber($subscriber,$extension);
        }
        $subscriberClass = Utils::getBaseClassName($subscriber);

        if ($subscriber instanceof EventSubscriberInterface) {
            $events = $subscriber->getSubscribedEvents();
            foreach ($events as $event) {
                $this->subscribe($event['event_name'],$subscriberClass,$extension,$event['priority'],$event['handler_method']);
            }
            return true;
        }

        if ($subscriber instanceof EventListenerInterface) {
            if (empty($subscriber->getEventName()) == false) {
                $this->subscribe($subscriber->getEventName(),$subscriberClass,$extension,$subscriber->getPriority(),null);
            }
        }

        return false;
    }

    /**
     * Save subscriber info to db table. 
     *
     * @param string $eventName
     * @param string $class
     * @param string|null $extension
     * @param integer $priority
     * @param string|null $hadnlerMethod
     * @return bool
     */
    public function subscribe(
        string $eventName, 
        string $class, 
        ?string $extension = null, 
        int $priority = 0, 
        ?string $hadnlerMethod = null
    ): bool
    {
        return $this->subscriberRegistry->addSubscriber($eventName,$class,$extension,$priority,$hadnlerMethod);
    }

    /**
     * Subscribe callback
     *
     * @param string $eventName
     * @param Closure $callback
     * @param boolean $single
     * @return void
     */
    public function subscribeCallback(string $eventName, $callback, bool $single = false): void
    {        
        if (isset($this->subscribers[$eventName]) == false) {
            $this->subscribers[$eventName] = [];
        }
        if ($single == true) {
            $this->subscribers[$eventName] = [$callback];
        } else {
            $this->subscribers[$eventName][] = $callback;
        }
    }

    /**
     * Fire event, dispatch event data to all subscribers
     *
     * @param string $eventName
     * @param array|EventInterface $event
     * @param boolean $callbackOnly
     * @param string|null $extension
     * @return array
     */
    public function dispatch(string $eventName, $event = [], bool $callbackOnly = false, ?string $extension = null): array
    {       
        if (\is_object($event) == false) {
            $event = new Event($event);   
        }

        if (($event instanceof EventInterface) == false) {
            throw new Exception('Not valid event object.',1);
        }

        $event->setName($eventName);          
        $result = [];

        if ($callbackOnly != true) {
            // get all subscribers for event           
            $subscribers = $this->getSubscribers($eventName,$extension,1);
                     
            $this->log('Dispatch event ' . $eventName,$event->toArray());
            $result = $this->executeEventHandlers($subscribers,$event);  
        }

        // run subscribed callback
        $callbackResult = $this->runCallback($eventName,$event);

        return \array_merge($result,$callbackResult);
    }

    /**
     * Execute closure subscribers
     *
     * @param string $eventName
     * @param EventInterface $event
     * @return array
     */
    private function runCallback(string $eventName, $event): array
    {
        if (isset($this->subscribers[$eventName]) == false) {
            return [];
        }
        $result = [];
        foreach ($this->subscribers[$eventName] as $callback) {
            if (Utils::isClosure($callback) == true) {
                $callbackResult = $callback($event);
                $result[] = $callbackResult;
            }                  
        }

        return $result;
    }

    /**
     * Run event handlers
     *
     * @param array $eventSubscribers
     * @param EventInterface $event
     * @return array 
     */
    private function executeEventHandlers(array $eventSubscribers, Event $event): array
    {       
        $result = [];

        foreach ($eventSubscribers as $item) {
            $subscriber = Factory::createInstance($item['handler_class']);
            $handlerMethod = (empty($item['handler_method']) == true) ? 'execute' : $item['handler_method'];
           
            if ($subscriber instanceof EventSubscriberInterface) {
                // check for subscriber log
                if ($subscriber instanceof EventLogInterface) {
                    $this->log($subscriber->getLogMessage(),$subscriber->getLogContext());
                }

                $eventResult = $subscriber->{$handlerMethod}($event);
                // logging
                if (empty($eventResult) == false) {
                    $result[] = $eventResult;
                }              
            }
        }

        return $result;
    }

    /**
     * Log
     *
     * @param string $message
     * @param array $context
     * @return boolean
     */
    private function log(string $message, array $context = []): bool
    {
        if (empty($this->logger) == true) {
            return false;
        }
        if (($this->options['log'] ?? false) == true) {
            return $this->logger->info($message,$context);
        }
      
        return false;
    }
}
