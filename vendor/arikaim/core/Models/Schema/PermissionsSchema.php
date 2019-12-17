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
 * Permissions database table schema definition.
*/
class PermissionsSchema extends Schema  
{    
    /**
     * Db table name
     *
     * @var string
     */ 
    protected $tableName = "permissions";

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
        $table->string('extension_name')->nullable(true);
        $table->string('title')->nullable(true);
        $table->string('description')->nullable(true);        
        // indexes         
        $table->unique('name');
        $table->index('extension_name');
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
