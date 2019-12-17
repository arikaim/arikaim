<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Access\Traits;

/**
 *  Password trait
 *  Change password attribute name in model: protected $passwordColumn = 'password';
 *  Chage encrypth algo: protected $passwordEncryptAlgo = algo | null  
*/
trait Password 
{   
    /**
     * Encrypt password
     *
     * @param string $password
     * @param integer $algo
     * @return string
     */
    public function encryptPassword($password, $algo = null) 
    {
        $algo = ($algo == null) ? $this->getEncryptPasswordAlgo() : $algo;

        return (empty($algo) == true) ? $password : password_hash($password,$algo);
    }

    /**
     * Change password
     *
     * @param string|integer $id
     * @param string $password
     * @return bool
     */
    public function changePassword($id, $password)
    {       
        $model = $this->findById($id);
        if (is_object($model) == false) {
            return false;
        }
        $model->{$this->getPasswordAttributeName()} = $this->encryptPassword($password);  

        return $model->save();
    }    

    /**
     * Return true if password is correct.
     *
     * @param string $password   
     * @return bool
     */
    public function verifyPassword($password) 
    {
        if (empty($password) == true) {
            return false;
        }
        $hash = $this->getPassword();
        $algo = $this->getEncryptPasswordAlgo();

        return (empty($algo) == true) ? ($password == $hash) : password_verify($password,$hash);      
    }

    /**
     * Return password attribute name
     *
     * @return string
     */
    public function getPasswordAttributeName()
    {
        return (isset($this->passwordColumn) == true) ? $this->passwordColumn : 'password';
    }

    /**
     * Return encrypt algo
     *
     * @return mixed
     */
    public function getEncryptPasswordAlgo()
    {
        return (isset($this->passwordEncryptAlgo) == true) ? $this->passwordEncryptAlgo : PASSWORD_BCRYPT;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->{$this->getPasswordAttributeName()};
    }
}
