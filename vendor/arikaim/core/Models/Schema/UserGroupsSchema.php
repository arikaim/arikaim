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
 * User groups database table schema definition.
*/
class UserGroupsSchema extends Schema  
{   
    /**
     * Db table name
     *
     * @var string
     */  
    protected $tableName = "user_groups";

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
        $table->string('title')->nullable(false);
        $table->string('description')->nullable(true);   
        // unique indexes
        $table->unique('title');                       
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
