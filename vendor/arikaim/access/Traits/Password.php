<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     Access
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
    public function encryptPassword(string $password, $algo = null): string 
    {
        $algo = $algo ?? $this->getEncryptPasswordAlgo();

        return (empty($algo) == true) ? $password : \password_hash($password,$algo);
    }

    /**
     * Change password
     *
     * @param string|integer $id
     * @param string $password
     * @return bool
     */
    public function changePassword($id, string $password): bool
    {       
        $model = $this->findById($id);
        if ($model == null) {
            return false;
        }
        $model->{$this->getPasswordAttributeName()} = $this->encryptPassword($password);  
        
        return (bool)$model->save();
    }    

    /**
     * Return true if password is correct.
     *
     * @param string $password   
     * @return bool
     */
    public function verifyPassword(string $password): bool 
    {
        if (empty($password) == true) {
            return false;
        }
        $hash = $this->getPassword();
        $algo = $this->getEncryptPasswordAlgo();

        return (empty($algo) == true) ? ($password == $hash) : \password_verify($password,$hash);      
    }

    /**
     * Return password attribute name
     *
     * @return string
     */
    public function getPasswordAttributeName(): string
    {
        return $this->passwordColumn ?? 'password';
    }

    /**
     * Return encrypt algo
     *
     * @return mixed
     */
    public function getEncryptPasswordAlgo()
    {
        return $this->passwordEncryptAlgo ?? PASSWORD_BCRYPT;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->{$this->getPasswordAttributeName()};
    }
}
