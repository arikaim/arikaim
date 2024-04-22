<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Prototypes\Table;

use Arikaim\Core\Db\Interfaces\BlueprintPrototypeInterface;

/**
 * Document table prototype class
*/
class Document implements BlueprintPrototypeInterface
{
    /**
     * Build table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @param mixed $options (source table, callback)
     * @return void
     */
    public function build($table,...$options)
    {             
        $callback = $options[0] ?? null;
        $currencyTableName =  $options[1] ?? 'currency';         

        // columns
        $table->id();
        $table->prototype('uuid');              
        $table->status();
        $table->userId(true);    
        $table->relation('currency_id',$currencyTableName); 
        $table->string('external_id')->nullable(true);
        $table->string('api_driver')->nullable(true);
        $table->string('external_client')->nullable(true); 
        $table->integer('document_number')->nullable(true);   
        // added totals
        $table->total();
        $table->total('sub_total');

        $table->dateCreated();
        $table->dateUpdated();
        $table->dateDeleted();

        // unique      
        $table->unique(['document_number','user_id']);
        $table->unique(['external_id','api_driver']);

        if (\is_callable($callback) == true) {         
            $call = function() use($callback,$table) {
                $callback($table);                                 
            };
            $call();
        }
    }
}
