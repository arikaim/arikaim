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
class Category extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = 'category';

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
        $table->string('title')->nullable(false); 
        $table->text('description')->nullable(true);
        $table->string('meta_title')->nullable(true);
        $table->text('meta_description')->nullable(true);
        $table->text('meta_keywords')->nullable(true);   
        $table->string('branch')->nullable(true);
        $table->status();      
        $table->position();      
        $table->userId();  
        $table->slug(false,true);
        $table->relation('image_id','image',true);
        // index
        $table->index('branch');
        $table->unique(['slug','parent_id']);        
    }

    /**
     * Update table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function update($table) 
    {     
        if ($this->hasColumn('slug') == false) {
            $table->slug(false,true);
            $table->unique(['slug','parent_id']);        
        }

        if ($this->hasColumn('title') == false) {
            $table->string('title')->nullable(false);
        }

        if ($this->hasColumn('title') == false) {
            $table->string('title')->nullable(false);
        }
        if ($this->hasColumn('description') == false) {
            $table->text('description')->nullable(true);
        }
        if ($this->hasColumn('meta_title') == false) {
            $table->string('meta_title')->nullable(true);
        }
        if ($this->hasColumn('meta_description') == false) {
            $table->text('meta_description')->nullable(true);
        }
        if ($this->hasColumn('meta_keywords') == false) {
            $table->text('meta_keywords')->nullable(true);   
        }

        if ($this->hasColumn('branch') == false) {
            $table->string('branch')->nullable(true);
        }
        if ($this->hasColumn('image_id') == false) {
            $table->relation('image_id','image',true);
        }
    }
}
