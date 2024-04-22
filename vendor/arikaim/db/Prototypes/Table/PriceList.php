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
 * Price list table prototype class
*/
class PriceList implements BlueprintPrototypeInterface
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
        $productsTable = $options[0] ?? null;  
        $currencyTable = $options[1] ?? null;                          
        $callback = $options[2] ?? null;

        // columns
        $table->id();
        $table->prototype('uuid');              
        $table->string('key')->nullable(true);
        $table->integer('primary')->nullable(true)->default(null);

        if (empty($productsTable) == true) {
            $table->bigInteger('product_id')->unsigned()->nullable(true);  
            $table->index(['product_id']);
        } else {
            $table->relation('product_id',$productsTable);
        }
        
        if (empty($currencyTable) == true) {
            $table->bigInteger('currency_id')->unsigned()->nullable(true);  
            $table->index(['currency_id']);
        } else {
            $table->relation('currency_id',$currencyTable);
        }
       
        $table->price();

        // index     
        $table->unique(['product_id','key','currency_id']);
        $table->unique(['product_id','primary','currency_id']);

        if (\is_callable($callback) == true) {         
            $call = function() use($callback,$table) {
                $callback($table);                                 
            };
            $call();
        }
    }
}
