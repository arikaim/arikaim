<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db;

use Illuminate\Database\Capsule\Manager;

/**
 * Seed query
*/
class Seed 
{
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * Constructor
     *
     * @param string|null $tableName
     */
    public function __construct($tableName) 
    {             
        $this->tableName = $tableName;
    }

    /**
     * Create record if not exist
     *
     * @param array $search
     * @param array $values
     * @return boolean
     */
    public function create(array $search, array $values)
    {
        $query = Manager::table($this->tableName);     
        if ($query->where($search)->exists() == false) {
            return $query->insert(array_merge($search, $values));
        }

        return true;
    }

    /**
     * Create records from array 
     *
     * @param array $searchKeys
     * @param array $items
     * @param Closure|null $callback
     * @return boolean
     */
    public function createFromArray(array $searchKeys, array $items, $callback = null)
    {
        $errors = 0;     
        foreach ($items as $item) {
            $search = $this->createSearchValues($searchKeys,$item);

            if (is_callable($callback) == true) {
                $item = $callback($item);              
            }

            $result = $this->create($search,$item);
            $errors = ($result != true) ? $errors++ : $errors;
        }

        return ($errors == 0);
    }

    /**
     * Create search item values
     *
     * @param array $keys
     * @param array $item
     * @return array
     */
    protected function createSearchValues(array $keys, array $item)
    {
        $search = [];
        foreach ($keys as $key) {
            $search[$key] = $item[$key];
        }

        return $search;
    }

    /**
     * Update record
     *
     * @param array $search
     * @param array $values
     * @return boolean
     */
    public function update(array $search, array $values)
    {
        $query = Manager::table($this->tableName);     
        if ($query->where($search)->exists() == false) {
            return false;
        }

        return (bool)$query->take(1)->update($values);
    }

    /**
     * Update records from array 
     *
     * @param array $searchKeys
     * @param array $items
     * @param Closure|null $callback
     * @return boolean
     */
    public function updateFromArray(array $searchKeys, array $items, $callback = null)
    {
        $errors = 0;     
        foreach ($items as $item) {
            $search = $this->createSearchValues($searchKeys,$item);

            if (is_callable($callback) == true) {
                $item = $callback($item);              
            }
            
            $result = $this->update($search,$item);
            $errors = ($result != true) ? $errors++ : $errors;
        }

        return ($errors == 0);
    }

    /**
     * Update or create record
     *
     * @param array $search
     * @param array $values
     * @return boolean
     */
    public function updateOrCreate(array $search, array $values)
    {
        $query = Manager::table($this->tableName);   
        
        return $query->updateOrInsert($search,$values);
    }   

    /**
     * Update records from array 
     *
     * @param array $searchKeys
     * @param array $items
     * @param Closure|null $callback
     * @return boolean
     */
    public function updateOrCreateFromArray(array $searchKeys, array $items, $callback = null)
    {
        $errors = 0;     
        foreach ($items as $item) {
            $search = $this->createSearchValues($searchKeys,$item);

            if (is_callable($callback) == true) {
                $item = $callback($item);              
            }
            
            $result = $this->updateOrCreate($search,$item);
            $errors = ($result != true) ? $errors++ : $errors;
        }

        return ($errors == 0);
    }

    /**
     * Delete record
     *
     * @param array $search
     * @return boolean
     */
    public function delete(array $search)
    {
        $query = Manager::table($this->tableName);    

        $query = $query->where($search); 
        if ($query->exists() == true) {
            $result = $query->delete();
            return ($result != false);
        }

        return true;
    }
}
