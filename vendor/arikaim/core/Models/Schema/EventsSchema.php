<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Models\Schema;

use Arikaim\Core\Db\Schema;

/**
 * Events database table schema definition.
*/
class EventsSchema extends Schema  
{    
    /**
     * Db table name.
     *
     * @var string
     */
    protected $tableName = 'events';

    /**
     * Create table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function create($table) 
    {            
        // columns
        $table->id();
        $table->prototype('uuid');
        $table->status();
        $table->string('name')->nullable(true);
        $table->string('title')->nullable(true);
        $table->text('description')->nullable(true);
        $table->string('extension_name')->nullable(true);
        $table->text('properties')->nullable(true);
        // indexes
        $table->unique('name');
    }

    /**
     * Update table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function update($table)
    {       
        if ($this->hasColumn('properties') == false) {
            $table->text('properties')->nullable(true);
        }   
    }
}
