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
 * Queue database table schema definition.
 */
class QueueSchema extends Schema  
{   
    /**
     * Db table name
     *
     * @var string
     */ 
    protected $tableName = 'queue';

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
        $table->string('type',15)->nullable(true);
        $table->string('service_name')->nullable(true);
        $table->dateCreated();        
        $table->dateColumn('schedule_time');
        $table->dateColumn('date_executed');   
        $table->text('config')->nullable(true);
        $table->string('queue')->nullable(true);
        $table->userId();
        // indexes         
        $table->index('name');
        $table->index('recuring_interval');  
        $table->index('queue');  
    }

    /**
     * Update table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function update($table)
    {   
        // drop prev index
        if ($this->hasIndex('jobs_name_unique') == true) {
            $this->dropIndex('jobs_name_unique');
        }
        if ($this->hasColumn('config') == false) {
            $table->text('config')->nullable(true);
        } 
        if ($this->hasColumn('queue') == false) {
            $table->string('queue')->nullable(true);
        }    
        if ($this->hasColumn('type') == false) {
            $table->string('type',15)->nullable(true);
        } 
        if ($this->hasColumn('service_name') == false) {
            $table->string('service_name')->nullable(true);
        }   
        if ($this->hasColumn('user_id') == false) {
            $table->userId();
        }    
    }
}
