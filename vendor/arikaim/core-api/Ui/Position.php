<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     CoreAPI
*/
namespace Arikaim\Core\Api\Ui;

use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\Db\Model;

/**
 * Position Api controller
*/
class Position extends ApiController 
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
     * shift position
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function shift($request, $response, $data) 
    {       
        $this->requireControlPanelPermission();

        $model = $this->createModel($data);
        if ($model !== null) {          
            $targetModel = $model->findById($data->get('target_uuid'));
            if ($targetModel !== null) {                    
                $model->shiftPosition($targetModel);
            }
            $this->message('done');
        } else {
            $this->error('errors.position');
        }
       
        return $this->getResponse();
    }

    /**
     * Swap position
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function swap($request, $response, $data) 
    {
        $this->requireControlPanelPermission();

        $model = $this->createModel($data);

        if ($model !== null) {          
            $targetModel = $model->findById($data->get('target_uuid'));
            if ($targetModel !== null) {                    
                $model->swapPosition($targetModel);
            }
            $this->message('done');
        } else {
            $this->error('errors.position');
        }
       
        return $this->getResponse();
    }

    /**
     * Create model object form request data
     *
     * @param Validator $data
     * @return object|null
     */
    protected function createModel($data): ?object
    {
        $model = Model::create($data->get('model_name'));

        return ($model == null) ? null : $model->findById($data->get('uuid'));      
    }
}
