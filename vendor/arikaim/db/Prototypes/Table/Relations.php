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
 * Relations table prototype class
*/
class Relations implements BlueprintPrototypeInterface
{
    /**
     * Build table
     *
     * @param Arikaim\Core\Db\TableBlueprint $table
     * @param mixed $options (source feild, source table)
     * @return void
     */
    public function build($table,...$options)
    {                           
        // columns
        $table->id();
        $table->prototype('uuid');              
        $table->relation($options[0],$options[1],false);
        $table->relation($options[2],$options[3],false);

        $callback = (isset($options[4]) == true) ? $options[4] : null;
        if (is_callable($callback) == true) {         
            $call = function() use($callback,$table) {
                $callback($table);                                 
            };
            $call($table);
        }
    }
}
