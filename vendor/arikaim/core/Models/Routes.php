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

use Arikaim\Core\Routes\RoutesStorageInterface;

use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\Uuid;

/**
 * Routes database model
 */
class Routes extends Model implements RoutesStorageInterface
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
        'name',
        'pattern',
        'method',
        'handler_class',
        'handler_method',
        'extension_name',
        'redirect_url',
        'auth',
        'type',
        'status',
        'template_name',      
        'options',  
        'page_name'
    ];
    
    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'routes';

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Mutator (set) for options attribute.
     *
     * @param array:null $value
     * @return void
     */
    public function setOptionsAttribute($value)
    {
        $value = (is_array($value) == true) ? $value : [];    
        $this->attributes['options'] = json_encode($value);
    }

    /**
     * Mutator (get) for options attribute.
     *
     * @return array
     */
    public function getOptionsAttribute()
    {
        return json_decode($this->attributes['options'],true);
    }

    /**
     * Get routes
     *
     * @param array $filter  
     * @return array
     */
    public function getRoutes($filter = [])
    {
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);      
        }
       
        return (is_object($model) == true) ? $model->get()->toArray() : [];
    }

    /**
     * Delete routes
     *
     * @param array $filterfilter
     * @return boolean
     */
    public function deleteRoutes($filter = [])
    {
        $model = $this;

        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);                          
        }
        $result = $model->delete();

        return ($result !== false);
    }

    /**
     * Set routes status
     *
     * @param array     $filterfilter
     * @param integer   $status
     * @return boolean
     */
    public function setRoutesStatus($filter = [], $status)
    {
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = $model->where($key,'=',$value);
        }

        return (is_object($model) == true) ? $model->update(['status' => $status]) : false;
    }

    /**
     * Delete route
     *
     * @param string $method
     * @param string $pattern
     * @return bool
     */
    public function deleteRoute($method, $pattern)
    {       
        $model = $this->where('method','=',$method)->where('pattern','=',$pattern);
        if (is_object($model) == true) {
            $result = $model->delete();
            return ($result == null) ? true : $result;
        }

        return true;
    }

    /**
     * Get route
     *
     * @param string $method
     * @param string $pattern
     * @return array|false
     */
    public function getRoute($method, $pattern)
    {
        $model = $this->where('method','=',$method)->where('pattern','=',$pattern)->first();

        return (is_object($model) == false) ? false : $model->toArray();          
    }

    /**
     * Save route options
     *
     * @param string $method
     * @param string $pattern
     * @param array $options
     * @return boolean
     */
    public function saveRouteOptions($method, $pattern, array $options)
    {
        $model = $this->where('method','=',$method)->where('pattern','=',$pattern)->first();
        if (is_object($model) == true) {
            $model->options = $options; 
            
            return (bool)$model->save();
        }

        return false;
    }

    /**
     * Return true if reoute exists
     *
     * @param string $method
     * @param string $pattern
     * @return boolean
     */
    public function hasRoute($method, $pattern)
    {
        $model = $this->getRoute($method, $pattern);

        return ($model !== false);
    }

    /**
     * Add route
     *
     * @param array $route
     * @return bool
     */
    public function addRoute(array $route)
    {
        if ($this->hasRoute($route['method'],$route['pattern']) == false) {
            $model = $this->create($route);
            return is_object($model);
        }  
        $model = $this->where('method','=',$route['method'])->where('pattern','=',$route['pattern'])->first();
        $result = $model->update($route);  
        
        return ($result !== false);
    }

    /**
     * Return true if route info is valid
     *
     * @param array $routeInfo
     * @return boolean
     */
    public function isValid(array $routeInfo) 
    {
        return (
            isset($routeInfo['pattern']) == false
            || isset($routeInfo['handler_class']) == false
            || isset($routeInfo['handler_method']) == false 
            || empty(trim($routeInfo['type'])) == true
            || empty(trim($routeInfo['method'])) == true
        ) ? false : true;          
    }
}
