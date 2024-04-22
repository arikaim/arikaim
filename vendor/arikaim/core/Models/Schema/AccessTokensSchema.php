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
 * Access tokens database table schema definition.
*/
class AccessTokensSchema extends Schema  
{    
    /**
     * Db table name
     *
     * @var string
     */ 
    protected $tableName = 'access_tokens';

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
        $table->userId(false);
        $table->status();
        $table->string('token')->nullable(false);
        $table->integer('type')->nullable(false)->default(0);
        $table->dateCreated();
        $table->dateExpired();  
        // unique indexes
        $table->unique('token');
        $table->unique(['user_id','type']);
    }

    /**
     * Update table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function update($table) 
    {    
        if ($this->hasColumn('status') == false) {
            $table->status();
        }   
    }
}
