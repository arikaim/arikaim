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
     * @param array|string $value
     * @return void
     */
    public function setConfigAttribute($value)
    {
        $value = (\is_array($value) == true) ? $value : [$value];    

        $this->attributes['config'] = \json_encode($value);
    }

    /**
     * Mutator (get) for config attribute.
     *
     * @return array
     */
    public function getConfigAttribute()
    {
        return (empty($this->attributes['config']) == true) ? [] : \json_decode($this->attributes['config'],true);
    }

    /**
      * Add driver
      *
      * @param string $name     Driver name        
      * @param array  $data     Driver data
      * @return boolean
    */
    public function addDriver(string $name, array $data): bool
    {
        if ($this->hasDriver($name) == true) {
            $model = $this->findByColumn($name,'name');          
            $result = $model->update($data);
            
            return ($result !== false);
        }
        $data['name'] = $name;

        return ($this->create($data) != null);
    }

    /**
     * Remove driver
     *
     * @param string $name   
     * @return boolean
    */
    public function removeDriver(string $name): bool
    {
        $model = $this->findByColumn($name,'name');

        return ($model == null) ? true : (bool)$model->delete();
    }
    
    /**
     * Return true if driver exist
     *
     * @param string $name  
     * @return boolean
     */
    public function hasDriver(string $name): bool
    {
        return ($this->findByColumn($name,'name') != null);
    }

    /**
     * Save driver config
     *
     * @param string $name Driver name
     * @param array $config
     * @return boolean
     */
    public function saveConfig(string $name, array $config): bool
    {
        $model = $this->findByColumn($name,'name');
        if ($model != null) {
            $model->config = $config;
            return (bool)$model->save();
        }

        return false;
    }

    /**
     * Save driver config
     *
     * @param string $name Driver name
     * @param integer|string $status    
     * @return boolean
     */
    public function setDriverStatus(string $name, $status): bool
    {
        $model = $this->findByColumn($name,'name');

        return (empty($model) == false) ? $model->setStatus($status) : false;
    }

    /**
     * Get driver config
     *
     * @param string $name Driver name
     * @return array
     */
    public function getDriverConfig(string $name)
    {
        $model = $this->findByColumn($name,'name');
        
        return (empty($model) == false) ? $model->config : [];
    }

    /**
     * Get drivers list
     *
     * @param string|null $category
     * @param integer|null $status
     * @return array
     */
    public function getDriversList(?string $category = null, ?int $status = null): array
    {   
        $model = $this;
        if (empty($category) == false) {
            $model = $model->where('category','=',$category);
        }
        if (empty($status) == false) {
            $model = $model->where('status','=',$status);
        }
        $model = $model->get();

        return (empty($model) == false) ? $model->toArray() : [];
    }

    /**
     * Get driver
     *
     * @param string|integer $name Driver name
     * @return array|false
     */
    public function getDriver(string $name)
    {
        $model = $this->findByColumn($name,'name');
        
        return ($model != null) ? $model->toArray() : false;
    }
}
