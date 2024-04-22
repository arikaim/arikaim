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
 * Document items table prototype class
*/
class DocumentItems implements BlueprintPrototypeInterface
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
        $productsTableName = $options[1] ?? null;          
        $callback = $options[2] ?? null;

        // columns
        $table->id();
        $table->prototype('uuid');    
        $table->relation('document_id',$documentTableName);      
        $table->string('external_id')->nullable(true);
        $table->status();
        $table->position();
        $table->price(0.00);  

        if (empty($productsTableName) == false) {
            $table->relation('product_id',$productsTableName);   
            $table->unique(['document_id','product_id']); 
        } 
        $table->string('title')->nullable(true);
        $table->integer('qty')->nullable(false)->default(1);  
        $table->total();
        $table->dateCreated();
        $table->dateUpdated();

        if (\is_callable($callback) == true) {         
            $call = function() use($callback,$table) {
                $callback($table);                                 
            };
            $call();
        }
    }
}
