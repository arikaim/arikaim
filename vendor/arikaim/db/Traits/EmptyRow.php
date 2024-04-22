<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits;

/**
 * Empty row trait
*/
trait EmptyRow 
{    
    /**
     * Check if table how columns are empty
     *
     * @param array $skipColumns
     * @return boolean
     */
    public function isRowEmpty(array $skipColumns = []): bool
    {
        foreach ($this->getFillable() as $column) {

            if (\in_array($column,$skipColumns) == true) {
                continue;
            }

            if (empty($this->attributes[$column] ?? null) == false) {
                return false;
            }

        }

        return true;
    }
}
