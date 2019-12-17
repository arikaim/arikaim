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
 * Relation column prototype class
*/
class Relation implements BlueprintPrototypeInterface
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
        $nullable = (isset($options[2]) == false) ? false : $options[2];

        $table->bigInteger($options[0])->unsigned()->nullable($nullable);
        $table->foreign($options[0])->references('id')->on($options[1]);   
        $table->index($options[0]);
    }
}
