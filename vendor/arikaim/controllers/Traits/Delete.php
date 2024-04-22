<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Controllers\Traits;

use Arikaim\Core\Db\Model;

/**
 * Delete trait
*/
trait Delete 
{        
    /**
     * Get delete message name
     *
     * @return string
     */
    protected function getDeleteMessage(): string
    {
        return $this->deleteMessage ?? 'delete';
    }

    /**
     * Delete model
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function deleteController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $data
            ->addRule('text:min=2|required','uuid')           
            ->validate(true);  
 
        $uuid = $data->get('uuid');
        $model = Model::create($this->getModelClass(),$this->getExtensionName())->findById($uuid);
        $result = ($model == null) ? false : (bool)$model->delete();
            
        $this->setResponse($result,function() use($uuid) {              
            $this
                ->message($this->getDeleteMessage())
                ->field('uuid',$uuid);                  
        },'errors.delete');
    }
}
