<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Interfaces\Events\SubscriberRegistryInterface;
use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Utils\Uuid as UuidFactory;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;

/**
 * EventSubscribers database model
 */
class EventSubscribers extends Model implements SubscriberRegistryInterface  
{
    use Uuid,
        Find,
        Status;

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'handler_class',
        'handler_method',
        'extension_name',
        'priority'
    ];
    
    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'event_subscribers';

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Get subscribers list
     *
     * @param array $filter
     * @return array
     */
    public function getSubscribers(array $filter = [])
    {
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }

        return (is_object($model) == true) ? $model->get()->toArray() : [];
    }

    /**
     * Delete subscribers.
     *
     * @param array $filter
     * @return bool
     */
    public function deleteSubscribers(array $filter)
    {
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }

        return (is_object($model) == true) ? $model->delete() : false;
    }

    /**
     * Return true if event have subscriber(s)
     *
     * @param string $eventName
     * @param string $extension
     * @return boolean
     */
    public function hasSubscriber($eventName, $extension)
    {
        $model = $this->getSubscribersQuery($eventName,$extension)->get();
    
        return ($model->isEmpty() == false);   
    }

    /**
     * Return subscribers query builder
     *
     * @param string $eventName
     * @param string $extension
     * @return Builder
     */
    public function getSubscribersQuery($eventName, $extension)
    {
        $model = $this->where('name','=',$eventName);
        return $model->where('extension_name','=',$extension);       
    }

    
    

    /**
     * Save subscriber info to db table. 
     *
     * @param string $eventName
     * @param string $class
     * @param string|null $extension
     * @param integer $priority
     * @return bool
     */
    public function addSubscriber($eventName, $class, $extension = null, $priority = 0, $hadnlerMethod = null)
    {
        if ($this->hasSubscriber($eventName,$extension) == true) {
            return false;
        }
        $subscriber = [
            'uuid'           => UuidFactory::create(),
            'name'           => $eventName,
            'priority'       => (empty($priority) == true) ? 0 : $priority, 
            'extension_name' => $extension,
            'handler_class'  => Factory::getEventSubscriberClass($class,$extension),
            'handler_method' => $hadnlerMethod
        ];
      
        return $this->create($subscriber);       
    }   

    /**
     * Disable extension subscribers.
     *
     * @param string $extension
     * @return bool
     */
    public function disableExtensionSubscribers($extension) 
    {  
        return $this->changeStatus(null,$extension,Status::$DISABLED);
    }

    /**
     * Enable extension subscribers.
     *
     * @param string $extension
     * @return bool
     */
    public function enableExtensionSubscribers($extension) 
    {  
       return $this->changeStatus(null,$extension,Status::$ACTIVE);
    }

    /**
     * Enable all subscribers for event.
     *
     * @param string $eventName
     * @return bool
     */
    public function enableSubscribers($eventName)
    {
        return $this->changeStatus($eventName,null,Status::$ACTIVE);
    }

    /**
     * Disable all subscribers for event.
     *
     * @param string $eventName
     * @return bool
     */
    public function disableSubscribers($eventName)
    {
        return $this->changeStatus($eventName,null,Status::$DISABLED);
    }

    /**
     * Change subscriber status
     *
     * @param string $eventName
     * @param string $extension
     * @param string $status
     * @return bool
     */
    private function changeStatus($eventName = null, $extension = null, $status) 
    {
        if ($eventName != null) {
            $this->where('name','=',$eventName);
        }
        if ($extension != null) {
            $this->where('extension_name','=',$extension);
        }
        return $this->update(['status' => $status]);       
    }
}
