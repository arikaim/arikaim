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
 * Language database table schema definition.
 */
class LanguageSchema extends Schema  
{    
    /**
     * Db table name
     *
     * @var string
     */ 
    protected $tableName = "language";

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

        $table->string('code',10)->nullable(false);
        $table->string('code_3',3)->nullable(true);
        $table->string('country_code',20)->nullable(false);
        $table->string('title')->nullable(false);
        $table->string('native_title')->nullable(true);
        $table->prototype('defaultColumn');       
        // indexes
        $table->unique('code');
        $table->unique('code_3');      
        $table->index('country_code');        
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

    /**
     * Seeds
     *
     * @param Builder $query
     * @return void
     */
    public function seeds($query) 
    {
        $result = $query->updateOrInsert(['code' => 'en'],
        [
            "title"        => "English",
            "native_title" => "English",
            "code"         => "en",
            "code_3"       => "eng",
            "default"      => "1",
            "country_code" => "us"   
        ]);

        return $result;
    }
}
