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
     * Delete model
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function delete($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {                  
            $uuid = $data->get('uuid');
            $model = Model::create($this->getModelClass(),$this->getExtensionName())->findById($uuid);
            
            if (is_object($model) == true) {
                $result = $model->delete($status);
            }
            $result = ($result !== false);
        
            $this->setResponse($result,function() use($uuid) {              
                $this
                    ->message('delete')
                    ->field('uuid',$uuid);                  
            },'errors.delete');
        });
        $data
            ->addRule('text:min=2|required','uuid')           
            ->validate(); 

        return $this->getResponse();
    }
}
