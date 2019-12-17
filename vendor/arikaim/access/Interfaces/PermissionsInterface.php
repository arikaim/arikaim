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
 * Permissions interface
 */
interface PermissionsInterface
{    
    /**
     * Get user permission
     *
     * @param string $name
     * @param mixed $id
     * @param array $permissions
     * @return boolean
     */
    public function hasPermissions($name, $id, $permissions);

    /**
     * Add permission item.
     *
     * @param string $name    
     * @param string|null $title
     * @param string|null $description
     * @param string|null $extension
     * @return boolean
     */
    public function addPermission($name, $title = null, $description = null, $extension = null);
}
