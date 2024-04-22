<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2018 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Models\Schema;

use Arikaim\Core\Db\Schema;

/**
 * Logger classes registry
*/
class LogsSchema extends Schema  
{    
    /**
     * Db table name
     *
     * @var string
     */
    protected $tableName = 'logs';

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
        $table->integer('level')->nullable(true);
        $table->text('message')->nullable(true);
        $table->string('level_name')->nullable(true);
        $table->string('channel')->nullable(true);  
        $table->dateCreated();
        $table->text('context')->nullable(true);
        $table->text('extra')->nullable(true);
        // indexes            
        $table->index('level');
        $table->index('channel');
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
