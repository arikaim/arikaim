<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Controllers;

use Arikaim\Core\Controllers\ControlPanelApiInterface;
use Arikaim\Core\Controllers\ApiController;
use Exception;

/**
 * Base class for all Control Panel Api controllers
*/
class ControlPanelApiController extends ApiController implements ControlPanelApiInterface
{    
    /**
     * Constructor
     *
     * @param Container $container
     */
    public function __construct($container = null) 
    {
        parent::__construct($container);
        
        $this->requireControlPanelPermission();
    }


    /**
     * Rrun {method name}Controller function if exist
     *
     * @param string $name
     * @param array $arguments
     * @throws Exception
     * @return mixed
     */
    public function __call($name, $arguments)
    {  
        $name .= 'Controller';
        if (\method_exists($this,$name) == true) {
            $this->requireControlPanelPermission();
            // call method
            $this->resolveRouteParams($arguments[0]);
            ([$this,$name])($arguments[0],$arguments[1],$arguments[2]);

            return $this->getResponse();                 
        }

        throw new Exception('Route controller method not found.',1);
    }
}
