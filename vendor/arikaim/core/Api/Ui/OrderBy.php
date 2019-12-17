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
use Arikaim\Core\Db\OrderBy as OrderByColumn;

/**
 * Order by column api controller
*/
class OrderBy extends ApiController 
{
    /**
     * Set order by column
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setOrderBy($request, $response, $data) 
    {       
        $namespace = $data->get('namespace','');
        $field = $data->get('field');
        $type = $data->get('type');
        OrderByColumn::setOrderBy($field,$type,$namespace);

        $this
            ->field('order',OrderByColumn::getOrderBy($namespace))
            ->field('namespace',$namespace)
            ->field('type',$type);
            
        return $this->getResponse();
    }

    /**
     * Get order by column
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function getOrderBy($request, $response, $data) 
    {
        $namespace = $data->get('namespace',null);
        $this
            ->field('order',OrderByColumn::getOrderBy($namespace))
            ->field('namespace',$namespace);

        return $this->getResponse();
    }

    /**
     * Delete order by column
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function deleteOrderBy($request, $response, $data) 
    {
        $namespace = $data->get('namespace',null);        
        OrderByColumn::deleteOrderBy($namespace);

        return $this->field('namespace',$namespace)->getResponse();      
    }
}
