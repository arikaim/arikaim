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
 * Permission relations database table schema definition.
*/
class PermissionRelationsSchema extends Schema  
{    
    /**
     * Db table name
     *
     * @var string
     */ 
    protected $tableName = 'permission_relations';

    /**
     * Create table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function create($table) 
    {            
        $table->tablePolymorphicRelations('permission_id','permissions',function($table) {
            // columns                    
            $table->integer('read')->nullable(false)->default(0);
            $table->integer('write')->nullable(false)->default(0);
            $table->integer('delete')->nullable(false)->default(0);
            $table->integer('execute')->nullable(false)->default(0);              
        });       
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
