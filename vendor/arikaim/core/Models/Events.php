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

use Arikaim\Core\Interfaces\Events\EventRegistryInterface;
use Arikaim\Core\Db\Model as DbModel;
use Arikaim\Core\Utils\Uuid as UuidFactory;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\Find;

/**
 * Events database model
 */
class Events extends Model implements EventRegistryInterface
{
    use Uuid,
        Status,
        Find;
    
    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'title',
        'status',
        'extension_name',
        'properties',
        'description'
    ];
    
    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'events';

    /**
     * Timestamps disabled
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Save event properties description
     *
     * @param string $name
     * @param object|array $descriptor
     * @return boolean
     */
    public function savePropertiesDescriptor(string $name, $descriptor): bool
    {
        $event = $this->where('name','=',$name)->first();
        if ($event == null) {
            return false;
        }

        $properties = (\is_object($descriptor) == true) ? $descriptor->toArray() : $descriptor;

        return  $event->saveProperties($properties);
    }

    /**
     * Mutator (get) for properties attribute.
     *
     * @return array
     */
    public function getPropertiesAttribute()
    {
        $properties = $this->attributes['properties'] ?? null;

        return (empty($properties) == true) ? [] : \json_decode($properties,true);
    }

    /**
     * Save properties
     *
     * @param array $properties
     * @return boolean
     */
    public function saveProperties(array $properties): bool
    {
        $result = $this->update([
            'properties' => \json_encode($properties)
        ]);

        return ($result !== false);
    }

    /**
     * Deleet event
     *
     * @param string $name
     * @return bool
     */
    public function deleteEvent(string $name): bool 
    {           
        $model = $this->where('name','=',$name);

        return ($model->isEmpty() == false) ? (bool)$model->delete() : true;           
    }

    /**
     * Get event subscribers
     *
     * @param string|null $name
     * @return object
     */
    public function subscribers(?string $name = null)
    {
        $name = $name ?? $this->name;
        $model = DbModel::create('EventSubscribers');

        return $model->where('name','=',$name)->orWhere('name','=','*')->get();
    }

    /**
     * Delete events.
     *
     * @param array $filter
     * @return bool
     */
    public function deleteEvents(array $filter): bool
    {
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }

        return (bool)$model->delete();             
    }

    /**
     * Return true if event exist
     *
     * @param string $name
     * @return boolean
     */
    public function hasEvent(string $name): bool
    {
        return ($this->where('name','=',$name)->first() !== null);      
    }

    /**
     * Add or update event to events db table.
     *
     * @param string $name
     * @param string $title
     * @param string|null $extension
     * @param string|null $description
     * @return bool
     */
    public function registerEvent(string $name, string $title, ?string $extension = null, ?string $description = null): bool
    {
        $info = [
            'uuid'           => UuidFactory::create(),
            'name'           => $name,
            'extension_name' => $extension,
            'title'          => $title,
            'description'    => $description
        ];

        if ($this->hasEvent($name) == true) {
            $this->update($info);
            return true;
        } 
      
        return ($this->create($info) != null);
    }   

    /**
     * Get events list
     *
     * @param array $filter
     * @return array
     */
    public function getEvents(array $filter = []): array
    {
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }

        return $model->get()->toArray();
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
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }

        return (bool)$model->update(['status' => $status]);
    }
}
