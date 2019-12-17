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
 * Extensions database table schema definition.
 */
class ExtensionsSchema extends Schema  
{    
    /**
     * Db table name
     *
     * @var string
     */
    protected $tableName = "extensions";

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
        $table->string('alias')->nullable(true);            
        $table->string('title')->nullable(true);
        $table->text('description')->nullable(true);
        $table->string('short_description')->nullable(true);
        $table->string('version')->nullable(false);
        $table->string('class')->nullable(false);  
        $table->position();
        $table->type();           
        $table->text('admin_menu')->nullable(true);
        $table->text('console_commands')->nullable(true);
        $table->string('license_key')->nullable(true);
        // unique indexes        
        $table->unique('alias');
        $table->unique('license_key');
        $table->unique('name');
        // indexes          
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
