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
    public function applySearch($namespace = null)
    {
        return Arikaim\Core\Db\Search::apply($this,$namespace);
    }
}
