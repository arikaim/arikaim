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
 * User groups details database table schema definition.
*/
class UserGroupMembersSchema extends Schema  
{    
    /**
     * Db table name
     *
     * @var string
     */ 
    protected $tableName = 'user_group_members';

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
        $table->userId();
        $table->relation('group_id','user_groups',false);
        $table->prototype('uuid');     

        // date time   
        $table->dateCreated();
        $table->dateUpdated();
        $table->dateExpired();           
        // unique indexes
        $table->unique(['user_id','group_id']);   
    }

    /**
     * Update table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function update($table) 
    {        
        if ($this->hasColumn('uuid') == false) {
            $table->prototype('uuid');    
        } 
    }
}
