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
 * Permissions (Many to Many) table prototype class
*/
class Permissions implements BlueprintPrototypeInterface
{
    /**
     * Build table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @param mixed $options (reference feild, reference table, callback)
     * @return void
     */
    public function build($table,...$options)
    {                           
        // columns
        $table->id();
        $table->prototype('uuid');   
        $table->relation('entity_id',$options[0],false);     
        $table->relation('permission_id','permissions',true);     
        $table->string('relation_type')->nullable(false);         
        $table->integer('relation_id')->nullable(true);   
        $table->integer('read')->nullable(false)->default(0);
        $table->integer('write')->nullable(false)->default(0);
        $table->integer('delete')->nullable(false)->default(0);
        $table->integer('execute')->nullable(false)->default(0);        

        $table->index('relation_type');
        $table->index('relation_id');
        $table->unique(['relation_id','relation_type','entity_id'],'un_rel_id_type_' . $table->getTable());
        
        $callback = $options[1] ?? null;
        if (\is_callable($callback) == true) {         
            $call = function() use($callback,$table) {
                $callback($table);                                 
            };
            $call();
        }
    }
}
