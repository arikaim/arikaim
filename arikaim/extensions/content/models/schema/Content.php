<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Content\Models\Schema;

use Arikaim\Core\Db\Schema;

/**
 * content database table schema definition.
 */
class Content extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = 'content';

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
        $table->string('key')->nullable(false);
        $table->string('title')->nullable(true);
        $table->string('content_type')->nullable(false); 
        $table->string('content_id')->nullable(false); 
        // indexes         
        $table->unique(['key','user_id']);          
    }

    /**
     * Update table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function update($table) 
    {       
        if ($this->hasColumn('title') == false) {
            $table->string('title')->nullable(true);
        }
    }
}
