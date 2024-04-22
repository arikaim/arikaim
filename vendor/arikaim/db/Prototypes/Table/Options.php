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
 * Options table prototype class
*/
class Options implements BlueprintPrototypeInterface
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
        $optionTypeTable = $options[0] ?? null;     
        $referenceTable = $options[1] ?? null; 
        $callback = $options[2] ?? null;

        // columns
        $table->id();
        $table->prototype('uuid');              
        $table->string('key')->nullable(false);

        if (empty($optionTypeTable) == true) {
            $table->bigInteger('type_id')->unsigned()->nullable(true);  
            $table->index(['type_id']);
        } else {
            $table->relation('type_id',$optionTypeTable,true);
        }

        if (empty($referenceTable) == true) {
            $table->bigInteger('reference_id')->unsigned()->nullable(true);  
            $table->index(['reference_id']);
        } else {
            $table->relation('reference_id',$referenceTable);
        }
              
        $table->text('value')->nullable(true);
     
        // index
        $table->unique(['reference_id','key']);

        if (\is_callable($callback) == true) {         
            $call = function() use($callback,$table) {
                $callback($table);                                 
            };
            $call();
        }
    }
}
