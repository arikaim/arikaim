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
 * Jobs database table schema definition.
 */
class JobsSchema extends Schema  
{   
    /**
     * Db table name
     *
     * @var string
     */ 
    protected $tableName = "jobs";

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
        $table->string('handler_class')->nullable(false);
        $table->string('recuring_interval',50)->nullable(true);       
        $table->string('extension_name')->nullable(true);
        $table->integer('priority')->nullable(false)->default(0);
        $table->integer('executed')->nullable(false)->default(0);
        $table->dateCreated();        
        $table->dateColumn('schedule_time');
        $table->dateColumn('date_executed');                     
        // indexes         
        $table->unique('name');
        $table->index('recuring_interval');  
    }

    /**
     * Update table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function update($table)
    {       
    }
}
