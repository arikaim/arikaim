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
 * Paginate feed collection with unknow last page.
*/
class FeedsPaginator extends Paginator implements PaginatorInterface 
{
    /**
     * Constructor
     *
     * @param FeedCollection $source
     * @param integer $page
     * @param string|integer $perPage
     */
    public function __construct($source, $page = 1, $perPage = Paginator::DEFAULT_PER_PAGE)
    {                 
        $this->currentPage = $page;
        $this->perPage = $perPage;
        $this->items = $source->fetch($page,$perPage)->getItems();
     
        if (empty($source->getPageKey()) == true) {           
            $this->total = count($this->items);
            $this->items = $this->sliceItems($this->items);
            $this->lastPage = $this->calcLastPage();           
        } else {           
            $this->lastPage = Self::UNKNOWN;
            $this->total = Self::UNKNOWN;
        }      
    }
}
