<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Access\Interfaces;

/**
 * User auth interface
 */
interface UserProviderInterface
{    
    /**
     * Return unique id 
     *
     * @return mixed
     */
    public function getAuthId();

    /**
     * Get id name
     *
     * @return string
     */
    public function getAuthIdName();

    /**
     * Get user credentials
     *
     * @param array $credential
     * @return mixed|false
     */
    public function getUserByCredentials(array $credentials);

    /**
     * Return true if password is correct.
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password);

    /**
     * Return user details by auth id
     *
     * @param string|integer $id
     * @return array
     */
    public function getUserById($id);
}
