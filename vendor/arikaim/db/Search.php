<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db;

use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Db\SearchCondition;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Http\Session;

/**
 * Database search session helper
*/
class Search 
{
    /**
     * Return search value
     *
     * @param string $field
     * @param string|nmull $namespace
     * @return mixed|null
     */
    public static function getSearchValue($field, $namespace = null)
    {
        $search = Self::getSearch($namespace);
        return (isset($search[$field]) == true) ? $search[$field] : null;      
    }

    /**
     * Return current search text
     *
     * @param string|null $namespace
     * @return array
     */
    public static function getSearch($namespace = null)
    {
        return Session::get(Utils::createKey('search',$namespace),[]);      
    }

    /**
     * Remove all search condirtions
     *
     * @param string|null $namespace
     * @return void
     */
    public static function clearSearch($namespace = null)
    {
        Session::remove(Utils::createKey('search',$namespace));
    }

    /**
     * Set search data
     *
     * @param array $searchData
     * @param string|null $namespace
     * @return void
     */
    public static function setSearch($searchData, $namespace = null)
    {
        Session::set(Utils::createKey('search',$namespace),$searchData);      
    }

    /**
     * Return search field
     *
     * @param string $field
     * @param string|null $namespace
     * @return array|null
     */
    public static function getSearchCondition($field, $namespace = null)
    {
        $conditions = Self::getSearchConditions($namespace);
        return (isset($conditions[$field]) == true) ? $conditions[$field] : null;
    }

    /**
     * Return search field
     *
     * @param string|null $namespace
     * @return array
     */
    public static function getSearchConditions($namespace = null)
    {
        return Session::get(Utils::createKey('search.conditions',$namespace),[]); 
    }

    /**
     * Delete search condition
     *
     * @param string $field
     * @param string|null $namespace
     * @return void
     */
    public static function deleteSearchCondition($field, $namespace = null)
    {
        $conditions = Self::getSearchConditions($namespace);
        unset($conditions[$field]);
        Self::setSearchConditions($conditions,$namespace);
    }

    /**
     * Set search conditions
     *
     * @param array $conditions
     * @param string|null $namespace
     * @return void
     */
    public static function setSearchConditions($conditions, $namespace = null)
    {
        Session::set(Utils::createKey('search.conditions',$namespace),$conditions); 
    }

    /**
     * Set search field value
     *
     * @param string $field
     * @param mixed $searchFieldName
     * @param string $operator
     * @param string $queryOperator
     * @param string|null $namespace
     * @return void
     */
    public static function setSearchCondition($field, $namespace = null, $operator = null, $queryOperator = null, $searchFieldName = 'search_text')
    {
        $condition = SearchCondition::crate($field,$searchFieldName,$operator,$queryOperator);
        $conditions = Self::getSearchConditions($namespace);
        $conditions[$field] = $condition;
    
        Self::setSearchConditions($conditions,$namespace);
    }

    /**
     * Apply search conditions and return model object
     *
     * @param Builder|Model $builder
     * @param string|null $namespace
     * @return Builder
     */
    public static function apply($builder, $namespace = null)
    {    
        $conditions = Self::getSearchConditions($namespace); 
        foreach ($conditions as $condition) {          
            $builder = Self::applyCondition($builder,$condition,$namespace);            
        }
        
        return $builder;
    }

    /**
     * Apply search condition 
     *
     * @param Builder|Model $builder
     * @param array $condition
     * @param string|null $namespace
     * @return Builder
     */
    public static function applyCondition($builder, $condition, $namespace = null)
    {
        $search = Self::getSearch($namespace);
        $condition = SearchCondition::parse($condition,$search);

        if (empty($condition['search_value']) == false) {      
            if ($condition['query_operator'] == 'or') {
                $builder = $builder->orWhere($condition['field'],$condition['operator'],$condition['search_value']);
            } else {
                $builder = $builder->where($condition['field'],$condition['operator'],$condition['search_value']);
            }           
        } 

        return $builder;
    }
}
