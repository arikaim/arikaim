<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2018 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Models\Schema;

use Arikaim\Core\Db\Schema;

/**
 * Drivers classes registry
*/
class DriversSchema extends Schema  
{    
    /**
     * Db table name
     *
     * @var string
     */
    protected $tableName = 'drivers';

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
        $table->string('title')->nullable(true);
        $table->text('description')->nullable(true);
        $table->string('version',20)->nullable(true); 
        $table->string('class')->nullable(false);  
        $table->string('category')->nullable(true);
        $table->string('extension_name')->nullable(true);
        $table->string('module_name')->nullable(true);
        $table->text('config')->nullable(true);
        // indexes           
        $table->unique('class');
        $table->unique('name');           
        $table->index('extension_name');
        $table->index('module_name');
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
