<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Api\Ui;

use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\Paginator\SessionPaginator;

/**
 * Paginator Api controller
*/
class Paginator extends ApiController 
{
    /**
     * Set paginator current page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setPageController($request, $response, $data) 
    {       
        $namespace = $data->get('namespace',null);
        $page = $data->get('page',1);
        $paginator = SessionPaginator::getPaginator($namespace);

        if ($page == 'next') {                            
            $page = SessionPaginator::getCurrentPage($namespace) + 1;   
            $page = ($page > $paginator['last_page'] && $paginator['last_page'] != -1) ? $paginator['last_page'] : $page;  
        }
        if ($page == 'prev') {      
            $page = SessionPaginator::getCurrentPage($namespace) - 1; 
            $page = ($page < 1) ? 1 : $page;
        }
        SessionPaginator::setCurrentPage($page,$namespace);

        $this
            ->field('page',SessionPaginator::getCurrentPage($namespace))
            ->field('last_page',$paginator['last_page'])
            ->field('namespace',$namespace);      
    }

     /**
     * Clear paginator session data 
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function removeController($request, $response, $data) 
    {       
        $namespace = $data->get('namespace',null);
        SessionPaginator::clearPaginator($namespace);
        $this       
            ->field('namespace',$namespace);     
    }

    /**
     * Set paginator page size (rows per page)
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setPageSizeController($request, $response, $data) 
    {       
        $namespace = $data->get('namespace','');
        $pageSize = $data->get('page_size',1);
        SessionPaginator::setRowsPerPage($pageSize,$namespace);

        $this
            ->field('page_size',SessionPaginator::getRowsPerPage($namespace))
            ->field('namespace',$namespace);       
    }

    /**
     * Get current paginator page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function getPageController($request, $response, $data) 
    {
        $namespace = $data->get('namespace',null);
        $paginator = SessionPaginator::getPaginator($namespace);

        $this
            ->field('page',SessionPaginator::getCurrentPage($namespace))
            ->field('last_page',$paginator['last_page'])
            ->field('namespace',$namespace);       
    }

    /**
     * Set paginator view type
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function setViewTypeController($request, $response, $data) 
    {
        $namespace = $data->get('namespace',null);
        $view = $data->get('view',null);
        SessionPaginator::setViewType($view,$namespace);

        $this
            ->field('view',SessionPaginator::getViewType($namespace))
            ->field('namespace',$namespace);    
    }

    /**
     * Get paginator view type
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function getViewTypeController($request, $response, $data) 
    {
        $namespace = $data->get('namespace',null);
        
        $this
            ->field('view',SessionPaginator::getViewType($namespace))
            ->field('namespace',$namespace);      
    }
}
