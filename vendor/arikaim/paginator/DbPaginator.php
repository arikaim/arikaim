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

use Arikaim\Core\Paginator\PaginatorInterface;
use Arikaim\Core\Paginator\Paginator;

/**
 * Paginate Illuminate\Database\Eloquent\Builder objects
*/
class DbPaginator extends Paginator implements PaginatorInterface 
{
    /**
     * Constructor
     *
     * @param Builder $builder 
     * @param integer $page
     * @param integer $perPage
     */
    public function __construct($builder, $page, $perPage = Paginator::DEFAULT_PER_PAGE)
    {          
        $this->total = $builder->toBase()->getCountForPagination();
        $this->items = $this->total ? $builder->forPage($page,$perPage)->get(['*']) : [];
        $this->currentPage = $page;
        $this->perPage = $perPage;
        $this->lastPage = $this->calcLastPage();
    }
}
