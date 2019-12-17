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
 * Slug column prototype class
*/
class Slug implements BlueprintPrototypeInterface
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
        $unique = (isset($options[0]) == false) ? true : $options[0];

        $table->string('slug')->nullable(false);
        if ($unique == true) {
            $table->unique('slug');      
        } else {
            $table->index('slug');     
        }
    }
}
