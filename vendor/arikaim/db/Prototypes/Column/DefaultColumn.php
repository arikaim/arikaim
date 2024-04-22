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
 * Default column prototype class
*/
class DefaultColumn implements BlueprintPrototypeInterface
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
        $name = $options[0] ?? 'default';
        $keyColumn = $options[1] ?? null;

        $table->integer($name)->nullable(true)->default(null); 
        if (empty($keyColumn) == false) {
            $table->unique([$name,$keyColumn]);   
        } else {
            $table->unique([$name]);   
        }
    }
}
