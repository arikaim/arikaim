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
        // columns
        $table->id();
        $table->prototype('uuid');              
        $table->status();
        $table->position();
        $table->type();
        $table->relation('reference_id',$options[0],false);     
        $table->string('key')->nullable(false);
        $table->text('value')->nullable(true);
        $table->string('title')->nullable(true);
        $table->text('description')->nullable(true);
        $table->integer('hidden')->nullable(false)->default(0);
        $table->integer('readonly')->nullable(false)->default(0);
        $table->string('default')->nullable(true);
        $table->text('items')->nullable(true);
        $table->integer('items_reference_id')->nullable(true);
        $table->string('items_type')->nullable(true);
        // index
        $table->unique(['reference_id','key']);

        $callback = (isset($options[1]) == true) ? $options[1] : null;
        if (is_callable($callback) == true) {         
            $call = function() use($callback,$table) {
                $callback($table);                                 
            };
            $call($table);
        }
    }
}
