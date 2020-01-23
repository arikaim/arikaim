<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Category\Models\Traits;

use Arikaim\Core\Collection\Arrays;
use Arikaim\Extensions\Category\Models\Category;

/**
 * Category relations trait
*/
trait CategoryRelations 
{    
    /**
     * Category relations
     *
     * @return mixed
     */
    public function categories() 
    {
        return $this->morphToMany(Category::class,'relation','category_relations');       
    }

    /**
     * Get categories list
     *
     * @return array
     */
    public function getCategoriesList()
    {
        $result = [];
        foreach ($this->categories as $category) {           
            $result[] = Arrays::toString($category->getTitle());
        }
        
        return $result;
    }
}
