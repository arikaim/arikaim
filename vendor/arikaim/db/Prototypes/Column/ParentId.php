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

use Arikaim\Core\Db\Interfaces\BlueprintPrototypeInterface;

/**
 * Parent id relation column prototype class
*/
class ParentId implements BlueprintPrototypeInterface
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
        $columnName = $options[0] ?? 'parent_id';

        $table->bigInteger($columnName)->unsigned()->nullable(true);
        $table->foreign($columnName)->references('id')->on($table->getTable())->onDelete('cascade');     

        $table->index($columnName);
    }
}
