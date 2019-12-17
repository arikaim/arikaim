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
use Arikaim\Core\Db\Model;

/**
 * Users controller login, logout 
*/
class Users extends ApiController  
{   
    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('system:admin.messages.user');
    }

    /**
     * Control panel login
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function adminLogin($request, $response, $data) 
    {
        $this->onDataValid(function($data) {  
            $credentials = [
                    'user_name' => $data->get('user_name'),
                    'password' => $data->get('password')
            ];
            $result = $this->get('access')->authenticate($credentials);
      
            if ($result === false) {           
                $this->error('errors.login');   
            } else {        
                $access = $this->get('access')->hasControlPanelAccess();
                if ($access == false) {
                    $this->setError('errors.login');   
                } 
            }              
        });
        $data
            ->addRule("text:min=2","user_name")   
            ->addRule("text:min=2","password") 
            ->validate();
    
        return $this->getResponse();   
    }

    /**
     * Logout
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function logoutController($request, $response, $data) 
    {    
        $this->get('access')->logout();        
    }   

    /**
     * Control Panel change user details
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function changeDetails($request, $response, $data)
    {
        // access from contorl panel only 
        $this->requireControlPanelPermission();
         
        $this->onDataValid(function($data) { 
           
            $userName = $data->get('user_name');
            $email = $data->get('email',null);
            $logedUser = $this->get('access')->getUser();
            $user = Model::Users();

            // check if user name is changed           
            if ($logedUser['user_name'] != $userName) {
                // check if user name exists              
                if ($user->verifyUserName($userName,$logedUser['id']) == false) {                   
                    return $this->error('errors.username');                                                                                   
                }
            }
            if (empty($email) == false) {
                if ($user->verifyEmail($email,$logedUser['id']) == false) {                   
                    return $this->error('errors.email');                                                                                   
                }
            }
            $info = [
                'user_name' => $userName,
                'email'     => $email
            ];
          
            $result = $user->findById($logedUser['id'])->update($info);
            if ($result == false) {
                return $this->error('errors.update');                    
            }

             // check for change password 
             $password = $data->get('password',null);
             if (empty($password) == false) {
                $newPassword = $data->get('new_password');
                $repeatPassword = $data->get('repeat_password');
                $user = $user->findById($logedUser['id']);
                
                if ($user->verifyPassword($password) == false) {                  
                    return $this->error('errors.invalid');                  
                } 
                if ($newPassword != $repeatPassword) {
                    // passwords not mach            
                    return $this->error('errors.password');                                   
                }
              
                $result = $user->changePassword($logedUser['id'],$newPassword);
            } 
            $this->setResponse($result,'update','errors.update'); 
        });
        $data 
            ->addRule("text:min=2|required","user_name") 
            ->addRule("email","email")           
            ->addRule("text:min=5","old_password")
            ->addRule("text:min=5","new_password")
            ->addRule("text:min=5","repeat_password")
            ->validate();

        return $this->getResponse();    
    }
}
