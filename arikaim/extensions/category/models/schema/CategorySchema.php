<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Category\Models\Schema;

use Arikaim\Core\Db\Schema;

/**
 * Category db table
 */
class CategorySchema extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = "category";

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
        $table->parentId();
        $table->prototype('uuid');       
        $table->string('branch')->nullable(true);
        $table->status();
        $table->position();      
        $table->userId();       
        // index
        $table->index('branch');
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
