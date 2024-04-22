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
 * Image thumbnails table schema definition.
 */
class ImageThumbnails extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = 'image_thumbnails';

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
        $table->relation('image_id','image');
        $table->string('folder')->nullable(true);
        $table->string('mime_type')->nullable(true);
        $table->string('file_name')->nullable(true);       
        $table->string('url')->nullable(true);       
        $table->integer('width')->nullable(true);
        $table->integer('height')->nullable(true);       
        $table->dateCreated();
        // indexes        
        $table->unique('file_name');
        $table->unique(['image_id','file_name']);
        $table->unique(['image_id','width','height']);
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
