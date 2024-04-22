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
    public function __construct($builder, int $page = 1, int $perPage = Paginator::DEFAULT_PER_PAGE)
    {          
        $total = $builder->toBase()->getCountForPagination();

        parent::__construct(
            $page,
            $total ? $builder->forPage($page,$perPage)->get(['*']) : [],
            $perPage,
            null,
            $total
        );   
    }   
}
