<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Image\Models\Schema;

use Arikaim\Core\Db\Schema;

/**
 * Image collections database table schema definition.
 */
class ImageCollections extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = 'image_collections';

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
        $table->userId();
        $table->status();
        $table->slug(false);
        $table->string('title')->nullable(false);
        $table->text('description')->nullable(true);
        $table->dateCreated();
        // indexes        
        $table->unique(['slug','user_id']); 
        $table->index(['status','user_id']);       
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
