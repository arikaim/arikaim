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
 * Options database table schema definition.
*/
class OptionsSchema extends Schema  
{    
    /**
     * Db table name.
     *
     * @var string
     */
    protected $tableName = "options";

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
        $table->string('key')->nullable(false);
        $table->text('value')->nullable(true);    
        $table->string('extension')->nullable(true);       
        $table->integer('auto_load')->nullable(false)->default(1);       
        // indexes
        $table->unique('key');  
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
