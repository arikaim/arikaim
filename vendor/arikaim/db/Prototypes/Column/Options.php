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
 * Options column prototype class
*/
class Options implements BlueprintPrototypeInterface
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
        $columnName = $options[0] ?? 'options';

        $table->text($columnName)->nullable(true);                   
    }
}
