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
use Arikaim\Core\Paginator\ArrayPaginator;

/**
 * Paginate Josn
*/
class JsonPaginator extends ArrayPaginator implements PaginatorInterface 
{
    /**
     * Constructor
     *
     * @param string $json
     * @param integer $page
     * @param integer $perPage
     */
    public function __construct($json, $page, $perPage = Paginator::DEFAULT_PER_PAGE)
    {      
        $items = json_decode($json,true);
        parent::__construct($items,$page,$perPage);       
    }
}
