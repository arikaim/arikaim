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
     * Subscribers scope query
     *
     * @param Builder $query
     * @param string|null $eventName
     * @param string|null $extensionName
     * @param integer|null $status
     * @return Builder
     */
    public function scopeSubscribers($query, ?string $eventName = null, ?string $extensionName = null, ?int $status = null)
    {      
        if (empty($status) == false) {
            $query->where('status','=',$status);
        }

        if (empty($eventName) == false) {
            $query->where(function($query) use($eventName) {
                $query
                    ->where('name','=',$eventName)
                    ->orWhere('name','=','*');
            });
        }
        if (empty($extensionName) == false) {
            $query->where('extension_name','=',$extensionName);
        }

        return $query;
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
        $query = $this->subscribers($eventName,$extensionName,$status);

        return $query->get()->toArray();
    }

    /**
     * Delete subscribers.
     *
     * @param array $filter
     * @return bool
     */
    public function deleteSubscribers(array $filter): bool
    {
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }

        return (bool)$model->delete();
    }

    /**
     * Return true if event have subscriber(s)
     *
     * @param string $eventName
     * @param string|null $extension
     * @return boolean
     */
    public function hasSubscriber(string $eventName, ?string $extension): bool
    {
        $model = $this->getSubscribersQuery($eventName,$extension)->get();
    
        return ($model->isEmpty() == false);   
    }

    /**
     * Return subscribers query builder
     *
     * @param string $eventName
     * @param string|null $extension
     * @return Builder
     */
    public function getSubscribersQuery(string $eventName, ?string $extension)
    {
        $query = $this->where('name','=',$eventName);
        if (empty($extension) == false) {
            $query = $query->where('extension_name','=',$extension);       
        }
     
        return $query;
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
    public function addSubscriber(
        string $eventName, 
        string $class, 
        ?string $extension = null, 
        int $priority = 0, 
        ?string $hadnlerMethod = null
    ): bool
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
      
        return ($this->create($subscriber) != null);   
    }   

    /**
     * Disable extension subscribers.
     *
     * @param string $extension
     * @return bool
     */
    public function disableExtensionSubscribers(string $extension): bool 
    {  
        return $this->changeStatus(null,$extension,Status::$DISABLED);
    }

    /**
     * Enable extension subscribers.
     *
     * @param string $extension
     * @return bool
     */
    public function enableExtensionSubscribers(string $extension): bool 
    {  
       return $this->changeStatus(null,$extension,Status::$ACTIVE);
    }

    /**
     * Enable all subscribers for event.
     *
     * @param string|null $eventName
     * @param string|null $extension
     * @return bool
     */
    public function enableSubscribers(?string $eventName, ?string $extension = null): bool
    {
        return $this->changeStatus($eventName,$extension,Status::$ACTIVE);
    }

    /**
     * Disable all subscribers for event.
     *
     * @param string|null $eventName
     * @param string|null $extension
     * @return bool
     */
    public function disableSubscribers(?string $eventName, ?string $extension = null): bool
    {
        return $this->changeStatus($eventName,$extension,Status::$DISABLED);
    }

    /**
     * Change subscriber status
     *
     * @param string|null $eventName
     * @param string|null $extension
     * @param int $status
     * @return bool
     */
    private function changeStatus(?string $eventName = null, ?string $extension = null, int $status = 1): bool 
    {
        if ($eventName != null) {
            $this->where('name','=',$eventName);
        }
        if ($extension != null) {
            $this->where('extension_name','=',$extension);
        }
        
        return (bool)$this->update(['status' => $status]);       
    }
}
