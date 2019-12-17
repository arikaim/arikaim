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

use Arikaim\Core\Db\Model;
use Arikaim\Core\Controllers\ApiController;

/**
 * Access Tokens controller
*/
class AccessTokens extends ApiController
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
     * Delete token
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function deleteController($request, $response, $data) 
    {                
        // access from contorl panel only 
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {          
            $token = $data->get('token');
            $result = Model::AccessTokens()->removeToken($token);

            $this->setResponse($result,function() use($token) {
                $this
                    ->message('access_tokens.delete')
                    ->field('token',$token);
            },'errors.access_tokens.delete');
                     
        });
        $data
            ->addRule("exists:model=AccessTokens|field=token|required","token")
            ->validate();        
    }

    /**
     * Delete expired tokens
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function deleteExpiredController($request, $response, $data) 
    {                   
        // access from contorl panel only 
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {               
            $uuid = $data->get('uuid');
            $user = Model::Users()->findById($uuid);
            $result = Model::AccessTokens()->deleteExpired($user->id,null);

            $this->setResponse($result,function() use($uuid) {
                $this
                    ->message('access_tokens.expired')
                    ->field('user',$uuid);
            },'errors.access_tokens.expired');
        });
        $data
            ->addRule("exists:model=Users|field=uuid|required","uuid")
            ->validate();         
    }
}
