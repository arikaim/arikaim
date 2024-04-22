<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     CoreAPI
*/
namespace Arikaim\Core\Api\Ui;

use Arikaim\Core\Controllers\FastApiController;
use Arikaim\Core\Db\Search as SessionSearch;
use Arikaim\Core\Paginator\SessionPaginator;

/**
 * Search Api controller
*/
class Search extends FastApiController 
{
    /**
     * Set search conditions
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setSearch($request, $response, $data) 
    {
        $namespace = $data->get('namespace','');
        $search = $data->get('search',[]);

        SessionSearch::setSearch($search,$namespace);
        // reset current page
        SessionPaginator::setCurrentPage(1,$namespace);

        $this
            ->field('search',SessionSearch::getSearch($namespace))
            ->field('namespace',$namespace);     
    }

    /**
     * Set search conditions
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function getSearch($request, $response, $data) 
    {
        $namespace = $data->get('namespace','');
        $this
            ->field('search',SessionSearch::getSearch($namespace))
            ->field('namespace',$namespace);      
    }

    /**
     * Delete all search conditions
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function clearSearch($request, $response, $data) 
    {
        $namespace = $data->get('namespace','');

        SessionSearch::clearSearch($namespace);   

        $this
            ->field('message','Done')
            ->field('namespace',$namespace);      
    }
}
