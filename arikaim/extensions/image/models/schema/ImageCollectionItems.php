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
 * Image collection items database table schema definition.
 */
class ImageCollectionItems extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = 'image_collection_items';

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
        $table->relation('collection_id','image_collections');
        $table->relation('image_id','image',true);
        $table->dateCreated();
        // indexes        
        $table->unique(['collection_id','image_id']); 
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
