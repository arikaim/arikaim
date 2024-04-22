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
 * Modules database table schema definition.
 */
class ModulesSchema extends Schema  
{    
    /**
     * Db table name
     *
     * @var string
     */
    protected $tableName = 'modules';

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
        $table->position(); 
        $table->string('name')->nullable(false);  
        $table->string('title')->nullable(true);
        $table->text('description')->nullable(true);
        $table->string('short_description')->nullable(true);
        $table->string('version')->nullable(false);         
        $table->string('class')->nullable(false);    
        $table->string('facade_class')->nullable(true);   
        $table->string('facade_alias')->nullable(true);  
        $table->type();
        $table->integer('bootable')->nullable(true);
        $table->text('console_commands')->nullable(true);
        $table->string('category')->nullable(true);   
        $table->text('config')->nullable(true);
        $table->string('service_name')->nullable(true);            
        // unique indexes        
        $table->unique('facade_alias');
        $table->unique('facade_class');
        $table->unique('name');
        // indexes    
        $table->index('category');          
        $table->index('title');
        $table->index('class');           
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
