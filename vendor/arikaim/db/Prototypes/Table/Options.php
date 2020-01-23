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

use Arikaim\Core\Db\BlueprintPrototypeInterface;

/**
 * Options table prototype class
*/
class Options implements BlueprintPrototypeInterface
{
    /**
     * Build table
     *
     * @param Arikaim\Core\Db\TableBlueprint $table
     * @param mixed $options (source table, callback)
     * @return void
     */
    public function build($table,...$options)
    {              
        $optionTypeTable = (isset($options[0]) == true) ? $options[0] : null;     
        $referenceTable = (isset($options[1]) == true) ? $options[1] : null; 
        $callback = (isset($options[2]) == true) ? $options[2] : null;

        // columns
        $table->id();
        $table->prototype('uuid');              
        $table->string('key')->nullable(false);

        if (empty($optionTypeTable) == true) {
            $table->bigInteger('type_id')->unsigned()->nullable(false);  
            $table->index(['type_id']);
        } else {
            $table->relation('type_id',$optionTypeTable);
        }

        if (empty($referenceTable) == true) {
            $table->bigInteger('reference_id')->unsigned()->nullable(true);  
            $table->index(['reference_id']);
        } else {
            $table->relation('reference_id',$referenceTable);
        }
              
        $table->text('value')->nullable(true);
     
        // index
        $table->unique(['reference_id','type_id']);
        $table->unique(['reference_id','key']);

        if (\is_callable($callback) == true) {         
            $call = function() use($callback,$table) {
                $callback($table);                                 
            };
            $call($table);
        }
    }
}
