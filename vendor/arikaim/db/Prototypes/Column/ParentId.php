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

use Arikaim\Core\Db\BlueprintPrototypeInterface;

/**
 * Parent id relation column prototype class
*/
class ParentId implements BlueprintPrototypeInterface
{
    /**
     * Build column
     *
     * @param Arikaim\Core\Db\TableBlueprint $table
     * @param mixed $options
     * @return void
     */
    public function build($table,...$options)
    {       
        $columnName = (isset($options[0]) == false) ? 'parent_id' : $options[0];

        $table->bigInteger($columnName)->unsigned()->nullable(true);
        $table->foreign($columnName)->references('id')->on($table->getTable())->onDelete('cascade');     

        $table->index($columnName);
    }
}
