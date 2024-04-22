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
 * Jobs registry database table schema definition.
 */
class JobsRegistrySchema extends Schema  
{   
    /**
     * Db table name
     *
     * @var string
     */ 
    protected $tableName = 'jobs_registry';

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
        $table->string('name')->nullable(false);
        $table->string('title')->nullable(true);
        $table->text('description')->nullable(true);
        $table->string('category')->nullable(true);
        $table->string('handler_class')->nullable(false);
        $table->string('package_name')->nullable(true);       
        $table->string('package_type')->nullable(true);
        $table->options('properties');
        $table->dateCreated();      
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
        if ($this->hasColumn('category') == false) {
            $table->string('category')->nullable(true);
        } 
    }
}
