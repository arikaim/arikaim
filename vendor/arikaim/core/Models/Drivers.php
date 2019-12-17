<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2018 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Interfaces\Driver\DriverRegistryInterface;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;

/**
 * Drivers registry database model
 */
class Drivers extends Model implements DriverRegistryInterface
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
        'class',
        'name',
        'title',
        'class',
        'config',
        'version',
        'description',
        'category',
        'extension_name',
        'module_name',
        'config'
    ];
    
    /**
     * Timestamps fields disabled
     *
     * @var boolean
    */
    public $timestamps = false;
    
    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'drivers';

    /**
     * Mutator (set) for config attribute.
     *
     * @param array $value
     * @return void
     */
    public function setConfigAttribute($value)
    {
        $value = (is_array($value) == true) ? $value : [$value];    
        $this->attributes['config'] = json_encode($value);
    }

    /**
     * Mutator (get) for config attribute.
     *
     * @return array
     */
    public function getConfigAttribute()
    {
        return (empty($this->attributes['config']) == true) ? [] : json_decode($this->attributes['config'],true);
    }

    /**
      * Add driver
      *
      * @param string $name     Driver name        
      * @param array  $data     Driver data
      * @return boolean
    */
    public function addDriver($name, $data)
    {
        if ($this->hasDriver($name) == true) {
            $model = $this->findByColumn($name,'name');
            return $model->update($data);
        }
        $data['name'] = $name;

        return $this->create($data);
    }

    /**
     * Remove driver
     *
     * @param string $name   
     * @return boolean
    */
    public function removeDriver($name)
    {
        $model = $this->findByColumn($name,'name');

        return (is_object($model) == false) ? true : $model->delete();
    }
    
    /**
     * Return true if driver exist
     *
     * @param string $name  
     * @return boolean
     */
    public function hasDriver($name)
    {
        $model = $this->findByColumn($name,'name');
        
        return is_object($model);
    }

    /**
     * Save driver config
     *
     * @param string $name Driver name
     * @param array $config
     * @return boolean
     */
    public function saveConfig($name, $config)
    {
        $model = $this->findByColumn($name,'name');
        if (is_object($model) == true) {
            $model->config = $config;
            return $model->save();
        }

        return false;
    }

    /**
     * Save driver config
     *
     * @param string $name Driver name
     * @param integer $status    
     * @return boolean
     */
    public function setDriverStatus($name, $status)
    {
        $model = $this->findByColumn($name,'name');

        return $model->setStatus($status);
    }

    /**
     * Get driver config
     *
     * @param string $name Driver name
     * @return array
     */
    public function getDriverConfig($name)
    {
        $model = $this->findByColumn($name,'name');
        
        return (is_object($model) == true) ? $model->config : [];
    }

    /**
     * Get drivers list
     *
     * @param string|null $category
     * @param integer|null $status
     * @return array
     */
    public function getDriversList($category = null, $status = null)
    {   
        $model = $this;
        if (empty($category) == false) {
            $model = $model->where('category','=',$category);
        }
        if (empty($status) == false) {
            $model = $model->where('status','=',$status);
        }

        return $model->get()->toArray();
    }

    /**
     * Get driver
     *
     * @param string|integer $name Driver name
     * @return array|false
     */
    public function getDriver($name)
    {
        $model = $this->findByColumn($name,'name');
        
        return (is_object($model) == true) ? $model->toArray() : false;
    }
}
