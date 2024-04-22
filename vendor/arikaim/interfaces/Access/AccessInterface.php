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
     *  Full permissions
     */
    const FULL = ['read','write','delete','execute'];
    
    /**
     * Read
     */
    const READ      = ['read'];
    const WRITE     = ['write'];
    const DELETE    = ['delete'];
    const EXECUTE   = ['execute'];
    
    /**
     * Control panel permission
     */
    const CONTROL_PANEL = 'ControlPanel';

    /**
     * Check if current loged user have control panel access
     *
     * @param integer|null $authId 
     * @return boolean
     */
    public function hasControlPanelAccess($authId = null): bool;

    /**
     * Check access 
     *
     * @param string|int $name Permission name
     * @param string|array|null $type PermissionType (read,write,execute,delete)   
     * @param integer|null $authId 
     * @return boolean
    */
    public function hasAccess($name, $type = null, $authId = null): bool;

    /**
     * Resolve permission full name  name:type
     *
     * @param string $name
     * @return array
     */
    public function resolvePermissionName(string $name): array;

    /**
     * Control panel permission name
     *
     * @return string
     */
    public function getControlPanelPermission(): string;

    /**
     * Full Permissions 
     *
     * @return array
     */
    public function getFullPermissions(): array;

    /**
     * Get user permissions list
     *
     * @param integer|null $authId
     * @return mixed
     */
    public function getUserPermissions(?int $authId = null);

    /**
     * Add permission item.
     *
     * @param string $name    
     * @param string|null $title
     * @param string|null $description
     * @param string|null $extension
     * @return boolean
     */
    public function addPermission(string $name, ?string $title = null, ?string $description = null, ?string $extension = null): bool;
}
