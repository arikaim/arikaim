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
use Arikaim\Core\Queue\Cron;
use Arikaim\Core\Queue\QueueWorker;
use Arikaim\Core\System\Error\PhpError;

/**
 * Queue controller
*/
class Queue extends ApiController
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
     * Start queue worker
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function startWorkerController($request, $response, $data)
    {
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) {            
            $worker = new QueueWorker($this->get('queue'),$this->get('options'),$this->get('logger'));
            $result = $worker->runDaemon();
            
            $this->setResponse($result,'queue.run','errors.queue.run');
        });
        $data->validate();      
    }
    
    /**
     * Stop queue worker
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function stopWorkerController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {              
            $worker = new QueueWorker($this->get('queue'),$this->get('options'),$this->get('logger'));
            $result = $worker->stopDaemon();
            if ($result == false) {
                $error = PhpError::getPosixError();
                $this->error($error);
            } else {
                $this->nessage('queue.stop');
            }
           
        });
        $data->validate();          
    }

    /**
     * Delete jobs
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function deleteJobsController($request, $response, $data)
    {
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) {             
              //  Arikaim::jobs()->getQueueService()->removeAllJobs(); ( TOOD ) 
        });
        $data->validate();           
    }
    
    /**
     * Install cron scheduler entry
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function installCron($request, $response, $data)
    {
        $this->requireControlPanelPermission();
               
        $cron = new Cron();
        $result = ($cron->isInstalled() == false) ? $cron->install() : true;
           
        $this->setResponse($result,'cron.install','errors.cron.install');       
        return $this->getResponse();
    }

    /**
     * Uninstall cron scheduler entry
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function unInstallCron($request, $response, $data)
    {
        $this->requireControlPanelPermission();
               
        $cron = new Cron();
        $result = $cron->unInstall();

        $this->setResponse($result,'cron.uninstall','errors.cron.uninstall');       
        return $this->getResponse();
    }
}
