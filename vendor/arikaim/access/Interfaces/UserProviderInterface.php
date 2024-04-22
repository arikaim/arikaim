<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     Access
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
    public function getAuthIdName(): string;

    /**
     * Get user credentials
     *
     * @param array $credential
     * @return array|null
     */
    public function getUserByCredentials(array $credentials): ?array;

    /**
     * Return true if password is correct.
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword(string $password): bool;

    /**
     * Return user details by auth id
     *
     * @param string|integer $id
     * @return array|null
     */
    public function getUserById($id): ?array;
}
