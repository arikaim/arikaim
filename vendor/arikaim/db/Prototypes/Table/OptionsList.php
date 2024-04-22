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
 * Options list table prototype class
*/
class OptionsList implements BlueprintPrototypeInterface
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
        $callback = $options[1] ?? null;

        // columns
        $table->id();
        $table->prototype('uuid');              
        $table->position();
        $table->string('type_name')->nullable(false);
        $table->string('key')->nullable(false);
        $table->string('branch')->nullable(true);

        // unique      
        $table->unique(['type_name','key','branch']);

        if (\is_callable($callback) == true) {         
            $call = function() use($callback,$table) {
                $callback($table);                                 
            };
            $call();
        }
    }
}
