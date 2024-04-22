<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits\Options;

/**
 * Options list definition table trait
*/
trait OptionsListDefinition
{    
    /**
     * Get options list
     *
     * @param string $key
     * @return mixed
     */
    public function getByKey($key)
    {
        return $this->findByColumn($key,'key');
    }

    /**
     * Boot trait
     *
     * @return void
     */
    public static function bootOptionsList()
    {
        $fillable = [
            'type_name',
            'branch',
            'uuid',
            'position',
            'key'        
        ];

        static::retrieved(function($model) use ($fillable) {
            $model->fillable = \array_merge($model->fillable,$fillable);
        });

        static::saving(function($model) use ($fillable) {
            $model->fillable = \array_merge($model->fillable,$fillable);
        });
    }

    /**
     * Add item
     *
     * @param string $typeName
     * @param string $key
     * @param string|null $branch
     * @return Model|false
     */
    public function addItem($typeName, $key, $branch = null)
    {
        if ($this->hasItem($typeName,$key) == false) {
            return $this->create([
                'type_name' => $typeName,
                'key'       => $key
            ]);
        }

        return false;
    }

    /**
     * Get items
     *
     * @param string $typeName
     * @param string|null $branch
     * @return mixed
     */
    public function getItems($typeName, $branch = null)
    { 
        return $this->getItemsQuery($typeName,$branch)->orderBy('position')->get();
    } 

    /**
     * Get items query
     *
     * @param string $typeName
     * @param string|null $branch
     * @return QueryBuilder
     */
    public function getItemsQuery($typeName, $branch = null)
    {
        $query = $this->where('type_name','=',$typeName);
        $query = ($branch == null) ? $query->whereNull('branch') : $query->where('branch','=',$branch);

        return $query;
    } 

    /**
     * Return true if item exist
     *
     * @param string $typeName
     * @param string $key
     * @param string|null $branch
     * @return boolean
     */
    public function hasItem($typeName, $key, $branch = null)
    {
        $model = $this->where('type_name','=',$typeName)->where('key','=',$key);
        if (empty($branch) == false) {
            $model = $model->where('branch','=',$branch);
        }
         
        return $model->exists();
    }
}
