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
 * Slug column prototype class
*/
class Slug implements BlueprintPrototypeInterface
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
        $unique = $options[0] ?? true;
        $nullable = $options[1] ?? false;
        
        $table->string('slug')->nullable($nullable);
        if ($unique == true) {
            $table->unique('slug');      
        } 
        $table->index('slug');     
    }
}
