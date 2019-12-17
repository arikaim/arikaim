<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\Access;

/**
 * Auth interface
 */
interface AccessInterface
{    
    /**
     * Check if current loged user have control panel access
     *
     * @return boolean
     */
    public function hasControlPanelAccess($authId = null);

    /**
     * Check access 
     *
     * @param string $name Permission name
     * @param string|array $type PermissionType (read,write,execute,delete)    
     * @return boolean
    */
    public function hasAccess($name, $type = null, $authId = null);

    /**
     * Resolve permission full name  name:type
     *
     * @param string $name
     * @return array
     */
    public function resolvePermissionName($name);

    /**
     * Control panel permission name
     *
     * @return string
     */
    public function getControlPanelPermission();

    /**
     * Full Permissions 
     *
     * @return array
     */
    public function getFullPermissions();
}
