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
 * Sms content database table schema definition.
 */
class SmsContent extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = 'sms_content';

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
        $table->string('phone')->nullable(false);
        $table->text('message')->nullable(false);
        $table->dateCreated();
        $table->dateUpdated();
        // indexes   
        $table->index('phone');          
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
