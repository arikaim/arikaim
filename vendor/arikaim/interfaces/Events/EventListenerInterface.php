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
 * Event listener interface
 */
interface EventListenerInterface
{    
    /**
     * Run listener code.
     *
     * @param EventInterface $event
     * @return bool
     */
    public function execute($event);

    /**
     * Get event name
     *
     * @return string
     */
    public function getEventName();
    
    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority();    
}
