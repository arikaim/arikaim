<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Paginator;

use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Paginator\Paginator;
use Arikaim\Core\Http\Session;

/**
 * Paginator session helper
*/
class SessionPaginator 
{   
    /**
     * Return view type
     *
     * @param string|null $namespace
     * @param string|null $default
     * @return string
     */
    public static function getViewType(?string $namespace = null, ?string $default = Paginator::TABLE_VIEW)
    {
        return Session::get(Utils::createKey('paginator.view.type',$namespace),$default);
    }

    /**
     * Set view type
     *
     * @param string|null $namespace
     * @param string $type
     * @return void
     */
    public static function setViewType(string $type, ?string $namespace = null): void
    {
        $type = (empty($type) == true) ? Paginator::TABLE_VIEW : $type;
        
        Session::set(Utils::createKey('paginator.view.type',$namespace),$type);
    }

    /**
     * Get rows per page
     *
     * @param string|null $namespace
     * @return int
     */ 
    public static function getRowsPerPage(?string $namespace = null): int
    {
        $rows = Session::get(Utils::createKey('paginator.page',$namespace),Paginator::DEFAULT_PER_PAGE);

        return (empty($rows) == true) ? Paginator::DEFAULT_PER_PAGE : (int)$rows;
    }

    /**
     * Set rows per page value
     *
     * @param string|null $namespace
     * @param integer $rows
     * @return void
     */
    public static function setRowsPerPage(int $rows, ?string $namespace = null): void
    {
        $rows = ($rows < 1) ? 1 : $rows;          
        Session::set(Utils::createKey('paginator.page',$namespace),$rows);
    }

    /**
     * Return current page
     *
     * @param string|null $namespace
     * @return integer
     */
    public static function getCurrentPage(?string $namespace = null): int
    {
        $page = (integer)Session::get(Utils::createKey('paginator.current.page',$namespace),1);     
        return ($page < 1) ? 1 : $page;        
    }

    /**
     * Set current page
     *
     * @param string|null $namespace
     * @param integer $page
     * @return void
     */
    public static function setCurrentPage(int $page, ?string $namespace = null): void
    {
        $page = ($page < 1 || empty($page) == true) ? 1 : $page;       
        Session::set(Utils::createKey('paginator.current.page',$namespace),$page);       
    }

    /**
     * Create paginator
     *
     * @param object|array|json $source     
     * @param string|null $namespace
     * @param integer|null $pageSize
     * @param integer|null $currentPage
     * @return array
     */
    public static function create($source, ?string $namespace = null, ?int $pageSize = null, ?int $currentPage = null)
    {            
        $pageSize = (empty($pageSize) == true) ? Self::getRowsPerPage($namespace) : $pageSize;
        $currentPage = (empty($currentPage) == true) ? Self::getCurrentPage($namespace) : $currentPage;
               
        $paginator = Paginator::create($source,$currentPage,$pageSize);
        $data = $paginator->toArray();

        if ($paginator->getItemsCount() == 0 && $currentPage > 1) {         
            Self::setCurrentPage(1,$namespace);
            $paginator = Paginator::create($source,1,$pageSize);
            $data = $paginator->toArray();                  
        }             
        Self::savePaginator($namespace,$data['paginator']);
        
        return $paginator;
    }

    /**
     * Save paginator array in session
     *
     * @param string|null $namespace
     * @param array $data
     * @return void
     */
    public static function savePaginator(?string $namespace, $data): void
    {
        Session::set(Utils::createKey('paginator',$namespace),$data);          
    } 

    /**
     * Read paginator data from session
     *
     * @param string|null $namespace
     * @return array|null
     */
    public static function getPaginator(?string $namespace): ?array
    {
        $paginator = Session::get(Utils::createKey('paginator',$namespace),[]);  
        $paginator['current_page'] = Self::getCurrentPage($namespace);
     
        return $paginator;    
    } 

    /**
     * Clear paginator data
     *
     * @param string|null $namespace
     * @return void
     */
    public static function clearPaginator(?string $namespace): void
    {
        Session::remove(Utils::createKey('paginator',$namespace)); 
        Self::setCurrentPage(1,$namespace); 
    }
}
