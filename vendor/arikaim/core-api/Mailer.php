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
use Arikaim\Core\Utils\Utils;

/**
 * Mailer controller
*/
class Mailer extends ControlPanelApiController
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
    public function sendTestEmail($request, $response, $data)
    {
        $data->validate(true);
             
        $user = $this->get('access')->getUser();
        $componentName = $data->get('component','system:test');

        if (Utils::isEmail($user['email']) == false) {
            $this->setError('Control panel user email not valid!');
            return;
        }       
        
        $result = $this->get('mailer')->create($componentName)
            ->to($user['email'],'Admin User')                         
            ->send();

        $this->setResponse($result,'mailer.send',function() {
            $error = $this->get('mailer')->getErrorMessage();
            $error = (empty($error) == true) ? 'errors.mailer.send' : $error;

            $this->error($error);
        });           
    }
}
