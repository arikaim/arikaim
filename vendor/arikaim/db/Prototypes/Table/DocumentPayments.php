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
 * Document payments table prototype class
*/
class DocumentPayments implements BlueprintPrototypeInterface
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
        $documentTableName = $options[0] ?? null;    
        $callback = $options[1] ?? null;

        // columns
        $table->id();
        $table->prototype('uuid');    
        $table->relation('document_id',$documentTableName);  
        $table->string('transaction_id')->nullable(true);    
        $table->price(0.00,'amount');  
        $table->dateCreated();
        $table->dateUpdated();

        // index
        $table->unique('transaction_id');
        
        if (\is_callable($callback) == true) {         
            $call = function() use($callback,$table) {
                $callback($table);                                 
            };
            $call();
        }
    }
}
