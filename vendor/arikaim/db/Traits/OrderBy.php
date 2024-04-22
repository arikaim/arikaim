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

use Arikaim\Core\Db\OrderBy as OrderByClass;

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
    public function applyOrderBy(?string $namespace = null)
    {
        return OrderByClass::apply($this,$namespace);
    }
    
    /**
     * Random order query
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRandomOrder($query)
    {
        return $query->orderByRaw('RAND()');
    }
}
