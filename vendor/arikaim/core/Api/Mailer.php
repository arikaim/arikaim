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
use Arikaim\Core\Utils\Utils;

/**
 * Mailer controller (TOOD message)
*/
class Mailer extends ApiController
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
     * Send test email
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function sendTestEmailController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) { 
            
            $user = $this->get('access')->getUser();
            if (Utils::isEmail($user->email) == false) {
                $this->setError('Control panel user email not valid!');
                return $this->getResponse();
            }       
    
            $result = $this->get('mailer')->create()
                ->to($user->email,'Admin User')
                ->from($user->email,'Arikaim CMS')
                ->loadComponent('system:admin.emails.test')
                ->send();
            
            $this->setResponse($result,'mailer.send','errors.mailer.test');           
        });
        $data->validate();
    }
}
