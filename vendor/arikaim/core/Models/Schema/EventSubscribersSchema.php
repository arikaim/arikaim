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
 * EventSubscribers database table schema definition.
*/
class EventSubscribersSchema extends Schema  
{    
    /**
     * Db table name.
     *
     * @var string
     */
    protected $tableName = "event_subscribers";

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
        $table->string('name')->nullable(false);
        $table->string('handler_class')->nullable(false);
        $table->string('handler_method')->nullable(true);            
        $table->string('extension_name')->nullable(true);        
        $table->integer('priority')->nullable(false)->default(0);         
        // indexes         
        $table->unique(['name','handler_class']);
        $table->unique(['name','extension_name']);        
        $table->index('name'); 
        $table->index('priority'); 
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
