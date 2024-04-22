<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     CoreAPI
*/
namespace Arikaim\Core\Api;

use Arikaim\Core\Controllers\ControlPanelApiController;
use Arikaim\Core\App\ArikaimStore;
use Arikaim\Core\Http\ApiResponse;

/**
 * Arikaim store controller
*/
class Store extends ControlPanelApiController
{
    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('system:admin.messages');
    }

    /**
     * Save product order 
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function saveOrder($request, $response, $data)
    {
        $orderId = $data->get('order_id');
        $apiDriver = $data->get('api_driver');
    
        $response = $this->get('http')->post(ArikaimStore::ORDER_REGISTER_URL,[
            'form_params' => [
                'order_id'   => $orderId,
                'api_driver' => $apiDriver 
            ]           
        ]);
     
        $callResult = new ApiResponse($response); 
        if ($callResult->hasError() == true) {           
            $this->error('errors.store.order');          
            return $this->getResponse();          
        } 

        $product = $callResult->getField('product');
        $packages = $callResult->getField('packages');

        if ((isset($product['title']) == false) || (\count($packages) == 0)) {
            $this->error('errors.store.order');
            return $this->getResponse();
        }

        $store = new ArikaimStore();
        $store->getConfig()->set('product',$product);
        $store->getConfig()->set('packages',$packages);
        // save and reload config file
        $result = $store->getConfig()->save();

        $this->setResponse($result,function() use($product,$packages) {
            $this
                ->message('store.order')
                ->field('product',$product)
                ->field('packages',$packages);                            
        },'errors.store.order');   

        $this->get('cache')->clear();

        return $this->getResponse();
    }

    /**
     * Remove product order 
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function removeOrder($request, $response, $data)
    {
        $store = new ArikaimStore();
        $store->clear();
        $result = $store->getConfig()->save();

        $this->setResponse($result,'store.order.remove','errors.store.order.remove');     
        $this->get('cache')->clear();
        
        return $this->getResponse();
    }    
}
