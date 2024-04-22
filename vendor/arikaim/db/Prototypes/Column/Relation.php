<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Prototypes\Column;

use Illuminate\Database\Schema\ForeignKeyDefinition;
use Arikaim\Core\Db\Interfaces\BlueprintPrototypeInterface;

/**
 * Relation column prototype class
*/
class Relation implements BlueprintPrototypeInterface
{
    /**
     * Build column
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @param mixed $options
     * @return void
     */
    public function build($table,...$options)
    {       
        $nullable = $options[2] ?? false;
        $onDelete = $options[3] ?? null;
        $onUpdate = $options[4] ?? null;

        // addCommand 
        $table->bigInteger($options[0])->unsigned()->nullable($nullable);

        $index = $table->indexCommand('foreign',$options[0],null)->getAttributes();
        $foreign = new ForeignKeyDefinition($index);
        $foreign->references('id')->on($options[1]);   
      
        if (empty($onDelete) == false) {
            $foreign->onDelete($onDelete);
        }
        if (empty($onUpdate) == false) {
            $foreign->onUpdate($onUpdate);        
        }

        $table->addForeign($foreign); 
        $table->index($options[0]);
    }
}
