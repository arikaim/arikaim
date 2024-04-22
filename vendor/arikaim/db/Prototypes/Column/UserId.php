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
 * Id column prototype class
*/
class UserId implements BlueprintPrototypeInterface
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
        $nullable = $options[0] ?? true;
        $onDelete = $options[1] ?? null;
        $onUpdate = $options[2] ?? null;

        $table->bigInteger('user_id')->unsigned()->nullable($nullable);     
        $table->foreign('user_id')->references('id')->on('users'); 
        $table->index('user_id');
    }
}
