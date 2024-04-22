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

use Arikaim\Core\Db\Search as DbSearch;

/**
 * Search trait
*/
trait Search 
{    
    /**
     * Apply search conditions for current model
     *
     * @param string|null $namespace
     * @return Builder|Model
     */
    public function applySearch(?string $namespace = null)
    {
        return DbSearch::apply($this,$namespace);
    }
}
