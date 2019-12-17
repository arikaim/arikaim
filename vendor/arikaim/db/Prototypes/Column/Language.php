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
 * Language column prototype class
*/
class Language implements BlueprintPrototypeInterface
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
        $default = (isset($options[0]) == false) ? 'en' : $options[0];

        $table->string('language',3)->nullable(false)->default($default);      
        $table->index('language');   
    }
}
