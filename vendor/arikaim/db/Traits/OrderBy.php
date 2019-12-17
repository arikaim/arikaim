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
 * Order by column trait
*/
trait OrderBy 
{    
    /**
     * Apply order by to current model
     *
     * @param string|null $namespace
     * @return Builder|Model
     */
    public function applyOrderBy($namespace = null)
    {
        return Arikaim\Core\Db\OrderBy::apply($this,$namespace);
    }
}
