<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Dashboard\Controllers;

use Arikaim\Core\Controllers\ControlPanelApiInterface;
use Arikaim\Core\Controllers\ControlPanelApiController;
use Arikaim\Core\Db\Model;

/**
 * Dashboard control panel controller
*/
class DashboardControlPanel extends ControlPanelApiController implements ControlPanelApiInterface
{
    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('dashboard::admin.messages');
    }

    /**
     * Hide dashboard panel
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function hidePanelController($request, $response, $data)
    { 
        $data
            ->validate(true);
            
        $componentName = $data->get('component_name');
        $model = Model::Dashboard('dashboard');
        
        $result = $model->hidePanel($componentName);

        $this->setResponse($result,function() use($componentName) {            
            $this
                ->message('hide')
                ->field('component_name',$componentName);  
        },'errors.hide'); 
    }

    /**
     * Hide dashboard panel
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function showPanelController($request, $response, $data)
    { 
        $data
            ->validate(true);

        $componentName = $data->get('component_name');
        $model = Model::Dashboard('dashboard');
        
        $result = $model->showPanel($componentName);

        $this->setResponse($result,function() use($componentName) {            
            $this
                ->message('hide')
                ->field('component_name',$componentName);  
        },'errors.hide');
    }
}
