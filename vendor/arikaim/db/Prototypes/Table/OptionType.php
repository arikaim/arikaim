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
 * Option type table prototype class
*/
class OptionType implements BlueprintPrototypeInterface
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
        // columns
        $table->id();
        $table->prototype('uuid');                    
        $table->type();
        $table->string('key')->nullable(true);
        $table->string('title')->nullable(true);
        $table->text('description')->nullable(true);
        $table->integer('hidden')->nullable(false)->default(0);
        $table->integer('readonly')->nullable(false)->default(0);
        $table->string('default')->nullable(true);
        $table->text('items')->nullable(true);          
        $table->string('items_type')->nullable(true);
        $table->integer('rows')->nullable(true);
        $table->integer('cols')->nullable(true);
        $table->string('placeholder')->nullable(true);
        $table->integer('primary')->nullable(true);
        // 
        $table->string('data_source')->nullable(true);
        $table->string('data_source_type')->nullable(true);
        // index
        $table->unique(['key']);

        $callback = $options[0] ?? null;
        if (\is_callable($callback) == true) {         
            $call = function() use($callback,$table) {
                $callback($table);                                 
            };
            $call();
        }
    }
}
