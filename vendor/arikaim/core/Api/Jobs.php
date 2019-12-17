<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Api;

use Arikaim\Core\Controllers\ApiController;

/**
 * Jobs controller
*/
class Jobs extends ApiController
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
     * Delete job
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function deleteJobController($request, $response, $data)
    {
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) {       
            $uuid = $data->get('uuid');

            $job = $this->get('queue')->findById($uuid);
            $result = (is_object($job) == true) ? $job->delete() : false;

            $this->setResponse($result,function() use($uuid) {                  
                $this
                    ->message('jobs.delete')                   
                    ->field('uuid',$uuid);   
            },'errors.jobs.delete');
        });
        $data->validate();      
    }
    
    /**
     * Enable/Disable job
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setStatusController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {   
            $uuid = $data->get('uuid');  
            $status = $data->get('status',1);

            $job = $this->get('queue')->findById($uuid);
            $result = (is_object($job) == true) ? $job->setStatus($status) : false;
          
            $this->setResponse($result,function() use($job) {                  
                $this
                    ->message('jobs.status')
                    ->field('status',$job->status)
                    ->field('uuid',$job->uuid);   
            },'errors.jobs.status');
        });
        $data->validate();          
    }
}
