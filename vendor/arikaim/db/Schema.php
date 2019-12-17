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
use Illuminate\Database\Schema\Builder;

use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Db\TableBlueprint;
use PDOException;

/**
 * Database schema base class
*/
abstract class Schema  
{
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * Db storage engine
     *
     * @var string
     */
    protected $storageEngine = 'InnoDB';

    /**
     * Create table
     * 
     * @param  Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    abstract public function create($table);

    /**
     * Update existing table
     *
     * @param  Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    abstract public function update($table);

    /**
     * Insert or update rows in table
     *
     * @param Builder $query
     * @return void
     */
    public function seeds($query)
    {
    }

    /**
     * Constructor
     *
     * @param string|null $tableName
     */
    public function __construct($tableName = null) 
    {      
        if (empty($tableName) == false) {
            $this->tableName = $tableName;
        }
    }

    /**
     * Return table name
     *
     * @return string
     */
    public function getTableName() 
    {
        return $this->tableName;
    }
    
    /**
     * Return model table name
     *
     * @param string $class Model class name
     * @return boo|string
     */
    public static function getTable($class)
    {
        $instance = Factory::createSchema($class);
        return (is_object($instance) == false) ? false : $instance->getTableName();         
    }

    /**
     * Create table
     *    
     * @return void
     */
    public function createTable()
    {
        if ($this->tableExists() == false) {                                  
            $blueprint = new TableBlueprint($this->tableName,null);
            
            $call = function() use($blueprint) {
                $blueprint->create();

                $this->create($blueprint);            
                $blueprint->engine = $this->storageEngine;               
            };
            $call(); 
            $this->build($blueprint, Manager::schema());           
        }
    } 

    /**
     * Update table 
     *
     * @return void
     */
    public function updateTable() 
    {
        if ($this->tableExists() == true) {                           
            $blueprint = new TableBlueprint($this->tableName,null);
            
            $callback = function() use($blueprint) {
                $this->update($blueprint);                                 
            };
            $callback(); 
            $this->build($blueprint, Manager::schema());           
        }       
    } 
    
    /**
     * Execute seeds
     *
     * @return mixed|false
     */
    public function runSeeds()
    {
        if ($this->tableExists() == true) {  
            $query = Manager::table($this->tableName);          
            return $this->seeds($query);
        }

        return false;
    }

    /**
     * Return true if table is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        $query = Manager::table($this->tableName);     
        return empty($query->count() == true);
    }

    /**
     * Get query builder for table
     *
     * @param string $tableName
     * @return QueryBuilder
     */
    public static function getQuery($tableName)
    {
        return Manager::table($tableName);  
    }

    /**
     * Execute blueprint.
     *
     * @param  Arikaim\Core\Db\TableBlueprint  $blueprint
     * @param  Illuminate\Database\Schema\Builder  $builder
     * @return void
     */
    public function build($blueprint, Builder $builder)
    {
        $connection = $builder->getConnection();
        $grammar = $connection->getSchemaGrammar();
        $blueprint->build($connection,$grammar);
    }
    
    /**
     * Check if database exist.
     *
     * @param  object|string $model Table name or db model object
     * @return boolean
     */
    public static function hasTable($model)
    {      
        $tableName = (is_object($model) == true) ? $model->getTable() : $model;

        try {
            return Manager::schema()->hasTable($tableName);    
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Drop table
     * 
     * @param boolean $emptyOnly
     * @return boolean
     */
    public function dropTable($emptyOnly = true) 
    {
        if ($emptyOnly == true && $this->isEmpty() == true) {                  
            Manager::schema()->dropIfExists($this->tableName);
        } 
        if ($emptyOnly == false) {
            Manager::schema()->dropIfExists($this->tableName);           
        }
        return !$this->tableExists();
    } 

    /**
     * Checkif table exist
     *
     * @return bool
     */
    public function tableExists() 
    {
        return Manager::schema()->hasTable($this->tableName);
    }

    /**
     * Check if table column exists.
     *
     * @param string $column
     * @return boolean
     */
    public function hasColumn($column) 
    {
        return Manager::schema()->hasColumn($this->tableName,$column); 
    }
    
    /**
     * Return shema object
     *
     * @return object
     */
    public static function schema() 
    {
        return Manager::schema();
    }

    /**
     * Run Create and Update migration
     *
     * @param string $class
     * @param string $extension
     * @return bool
     */
    public static function install($class, $extension = null) 
    {                   
        $instance = Factory::createSchema($class,$extension);
        if (is_object($instance) == true) {
            try {
                if ($instance->tableExists() == false) {
                    $instance->createTable();
                }
              
                $instance->updateTable();
                $instance->runSeeds();

                return $instance->tableExists();
                
            } catch(\Exception $e) {
            }
        }
        
        return false;
    }

    /**
     * UnInstall migration
     *
     * @param string $class
     * @param string $extension
     * @param boolean $force Set to true will drop table if have rows.
     * @return bool
     */
    public static function unInstall($class, $extension = null, $force = false) 
    {                   
        $instance = Factory::createSchema($class,$extension);
        if (is_object($instance) == true) {
            try {
                return $instance->dropTable(!$force);
            } catch(\Exception $e) {
            }
        }
        return false;
    }
}   
