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
     * @param string|null $namespace
     * @param mixed $default
     * @return mixed|null
     */
    public static function getSearchValue(string $field, ?string $namespace = null, $default = null)
    {
        $search = Self::getSearch($namespace);
      
        return $search[$field] ?? $default;      
    }

    /**
     * Return current search text
     *
     * @param string|null $namespace
     * @return array
     */
    public static function getSearch(?string $namespace = null)
    {
        return Session::get(Utils::createKey('search',$namespace),[]);      
    }

    /**
     * Remove all search condirtions
     *
     * @param string|null $namespace
     * @return void
     */
    public static function clearSearch(?string $namespace = null): void
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
    public static function setSearch($searchData, ?string $namespace = null): void
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
    public static function getSearchCondition(string $field, ?string $namespace = null)
    {
        $conditions = Self::getSearchConditions($namespace);

        return $conditions[$field] ?? null;
    }

    /**
     * Return search field
     *
     * @param string|null $namespace
     * @return array
     */
    public static function getSearchConditions(?string $namespace = null)
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
    public static function deleteSearchCondition(string $field, ?string $namespace = null): void
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
    public static function setSearchConditions($conditions, ?string $namespace = null): void
    {
        Session::set(Utils::createKey('search.conditions',$namespace),$conditions); 
    }

    /**
     * Set search field value
     *
     * @param string $field
     * @param mixed $searchFieldName
     * @param string|null $operator
     * @param string|null $queryOperator
     * @param string|null $namespace
     * @return void
     */
    public static function setSearchCondition(
        string $field, 
        ?string $namespace = null, 
        ?string $operator = null, 
        ?string $queryOperator = null, 
        $searchFieldName = 'search_text'
    ): void
    {
        $condition = SearchCondition::create($field,$searchFieldName,$operator,$queryOperator);
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
    public static function apply($builder, ?string $namespace = null)
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
     * @param Builder $builder
     * @param array $condition
     * @param string|null $namespace
     * @return Builder
     */
    public static function applyCondition($builder, array $condition, ?string $namespace = null)
    {
        $search = Self::getSearch($namespace);
        $condition = SearchCondition::parse($condition,$search);

        if (empty($condition['search_value']) == false) {
            
            if ($condition['query_operator'] == 'or') {
                if ($condition['operator'] == 'ilike') {    
                    $searchValue = \mb_strtoupper($condition['search_value']);              
                    $builder = $builder->orWhereRaw('UPPER(' . $condition['field'] . ') LIKE ?',['%' . $searchValue . '%']);                   
                } else {
                    $builder = $builder->orWhere($condition['field'],$condition['operator'],$condition['search_value']);
                }
               
            } else {
                if ($condition['operator'] == 'ilike') {
                    $searchValue = \mb_strtoupper($condition['search_value']);       
                    $builder = $builder->whereRaw('UPPER(' . $condition['field'] . ') LIKE ?',['%' . $searchValue . '%']);                   
                } else {
                    $builder = $builder->where($condition['field'],$condition['operator'],$condition['search_value']);
                }                             
            }           
        } 
        
        return $builder;
    }
}
