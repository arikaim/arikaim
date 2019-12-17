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
        'name',
        'title',
        'extension_name',
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
     * Deleet event
     *
     * @param string $name
     * @return bool
     */
    public function deleteEvent($name) 
    {           
        $model = $this->where('name','=',$name);

        return ($model->isEmpty() == false) ? $model->delete() : true;           
    }

    /**
     * Get event subscribers
     *
     * @return object
     */
    public function subscribers()
    {
        $model = DbModel::create('EventSubscribers');
        $items = $model->where('name','=',$this->name)->get();
        
        return (is_object($model) == true) ? $items : $model;
    }

    /**
     * Delete events.
     *
     * @param array $filter
     * @return bool
     */
    public function deleteEvents(array $filter)
    {
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }

        return (is_object($model) == true) ? $model->delete() : false;             
    }

    /**
     * Return true if event exist
     *
     * @param string $name
     * @return boolean
     */
    public function hasEvent($name)
    {
        $model = $this->where('name','=',$name)->get();
      
        return !($model->isEmpty() == true);    
    }

    /**
     * Add event to events db table.
     *
     * @param string $name
     * @param string $title
     * @param string $extension
     * @param string $description
     * @return bool
     */
    public function registerEvent($name, $title, $extension = null, $description = null)
    {
        if ($this->hasEvent($name) == true) {
            return false;
        } 
        $info = [
            'name'           => $name,
            'extension_name' => $extension,
            'title'          => $title,
            'description'    => $description
        ];
        $model = $this->create($info);
         
        return is_object($model);
    }   

    /**
     * Get events list
     *
     * @param array $filter
     * @return array
     */
    public function getEvents(array $filter = [])
    {
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }

        return (is_object($model) == true) ? $model->get()->toArray() : [];
    }

    /**
     * Set events status
     *
     * @param array $filter
     * @param integer $status
     * @return boolean
     */
    public function setEventsStatus(array $filter = [], $status)
    {
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }

        return (is_object($model) == true) ? $model->update(['status' => $status]) : false;
    }
}
