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
 * PolymorphicRelation column prototype class
*/
class PolymorphicRelation implements BlueprintPrototypeInterface
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
        $nullable = $options[0] ?? false;
        $fieldName = $options[1] ?? 'relation';
        $uniqueField = $options[2] ?? null;

        $fieldId = $fieldName . '_id';
        $fieldType = $fieldName . '_type';

        $table->string($fieldType)->nullable($nullable);         
        $table->integer($fieldId)->nullable($nullable);   

        $table->index($fieldType);
        $table->index($fieldId);
        if (empty($uniqueField) == false) {
            $table->unique([$fieldId,$fieldType,$uniqueField],'un_rel_id_type_' . $table->getTable());
        }
    }
}
