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
 * Users database table schema definition.
*/
class UsersSchema extends Schema  
{    
    /**
     * Db table name
     *
     * @var string
     */ 
    protected $tableName = 'users';

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
        $table->string('email')->nullable(true);
        $table->string('user_name')->nullable(true);
        $table->string('password')->nullable(false);                     
        // date time
        $table->dateCreated();
        $table->dateUpdated();
        $table->dateDeleted();
        $table->dateColumn('date_login');
        $table->dateColumn('date_logout');
        // indexes
        $table->unique('email');
        $table->unique('user_name');
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
