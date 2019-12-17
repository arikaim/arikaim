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
 * Routes database table schema definition.
*/
class RoutesSchema extends Schema  
{    
    /**
     * Db table name
     *
     * @var string
     */ 
    protected $tableName = "routes";

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
        $table->string('pattern')->nullable(false);
        $table->string('method')->nullable(false);
        $table->string('handler_class')->nullable(false);
        $table->string('handler_method')->nullable(true);
        $table->string('extension_name')->nullable(true);
        $table->string('template_name')->nullable(true);
        $table->string('page_name')->nullable(true);
        $table->integer('auth')->nullable(true);
        $table->integer('type')->nullable(false)->default(0);      
        $table->text('options')->nullable(true);
        $table->string('redirect_url')->nullable(true);     
        // indexes           
        $table->unique(['pattern','method']);          
        $table->unique(['name','extension_name']);
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
