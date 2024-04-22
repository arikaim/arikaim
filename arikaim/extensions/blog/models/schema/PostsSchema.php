<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Blog\Models\Schema;

use Arikaim\Core\Db\Schema;

/**
 * Posts db table
 */
class PostsSchema extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = 'posts';

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
        $table->userId();
        $table->slug(false);
        $table->longText('content')->nullable(true);      
        $table->text('summary')->nullable(true);      
        $table->string('title')->nullable(true);
        $table->string('content_type')->nullable(true);    
        $table->integer('image_id')->nullable(true);     
        $table->dateCreated();
        $table->dateUpdated();
        $table->dateDeleted();
        $table->metaTags();
        // index
        $table->unique(['title','user_id']);
        $table->unique(['slug','user_id']);
    }

    /**
     * Update table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function update($table) 
    {
        if ($this->hasIndex('posts_slug_page_id_unique') == true) {
            $this->dropIndex('posts_slug_page_id_unique');
        }
        if ($this->hasIndex('posts_title_page_id_unique') == true) {
            $this->dropIndex('posts_title_page_id_unique');
        }
        if ($this->hasColumn('page_id') == true) {
            $table->dropForeign('posts_page_id_foreign');
            $table->dropColumn('page_id');
        }
        if ($this->hasColumn('meta_title') == false) {
            $table->metaTags();
        }  
        if ($this->hasColumn('image_id') == false) {
            $table->integer('image_id')->nullable(true);     
        }  
        if ($this->hasColumn('summary') == false) {
            $table->text('summary')->nullable(true);    
        }  
        if ($this->hasColumn('content_type') == false) {
            $table->string('content_type')->nullable(true); 
        }              
    }
}
