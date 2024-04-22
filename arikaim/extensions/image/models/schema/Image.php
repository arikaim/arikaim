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
 * Image database table schema definition.
 */
class Image extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = 'image';

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
        $table->relation('category_id','category',true);
        $table->string('file_name')->nullable(true);
        $table->string('base_name')->nullable(true);
        $table->string('mime_type')->nullable(true);
        $table->string('file_size')->nullable(true);
        $table->string('url')->nullable(true);
        $table->integer('private')->nullable(true); 
        $table->integer('width')->nullable(true); 
        $table->integer('height')->nullable(true); 
        $table->integer('deny_delete')->nullable(true); 
        $table->dateCreated();
        // indexes        
        $table->unique(['url','user_id']); 
        $table->unique(['file_name']);
        $table->index('mime_type');       
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
        if ($this->hasColumn('category_id') == false) {
            $table->relation('category_id','category',true);
        }           
    }
}
