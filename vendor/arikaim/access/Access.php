<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Access;

use Arikaim\Core\Interfaces\Access\AccessInterface;
use Arikaim\Core\Access\Interfaces\PermissionsInterface;

use Arikaim\Core\Collection\Arrays;

/**
 * Manage permissions.
 */
class Access implements AccessInterface
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
    const CONTROL_PANEL = "ControlPanel";
    
    /**
     * Permissions adapter
     *
     * @var PermissionsInterface
     */
    private $adapter;

    /**
     * Full Permissions 
     *
     * @return array
     */
    public function getFullPermissions()
    {
        return Self::FULL;
    }

    /**
     * Control panel permission name
     *
     * @return string
     */
    public function getControlPanelPermission()
    {
        return Self::CONTROL_PANEL;
    }

    /**
     * Constructor
     * 
     * @param PermissionsInterface $adapter
     */
    public function __construct(PermissionsInterface $adapter) 
    {
        $this->adapter = $adapter;         
    }

    /**
     * Set permissions adapter
     *
     * @param PermissionsInterface $adapter
     * @return void
     */
    public function setProvider(PermissionsInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Get permissions adapter
     *
     * @return PermissionsInterface
     */
    public function getAdapter()
    {        
        return $this->adapter;
    }
    
    /**
     * Check if current loged user have control panel access
     *
     * @param string|integer $authId
     * @return boolean
     */
    public function hasControlPanelAccess($authId = null)
    {
        return $this->hasAccess(Access::CONTROL_PANEL,ACCESS::FULL,$authId);
    }
    
    /**
     * Check access 
     *
     * @param string $name Permission name
     * @param string|array $type PermissionType (read,write,execute,delete)   
     * @param string|integer $authId 
     * @return boolean
     */
    public function hasAccess($name, $type = null, $authId = null)
    {       
        list($name, $permissionType) = $this->resolvePermissionName($name);
       
        if (is_array($permissionType) == false) {           
            $permissionType = $this->resolvePermissionType($type);
        }
    
        return $this->adapter->hasPermissions($name,$authId,$permissionType);            
    }

    /**
     * Add permission item.
     *
     * @param string $name    
     * @param string|null $title
     * @param string|null $description
     * @param string|null $extension
     * @return boolean
     */
    public function addPermission($name, $title = null, $description = null, $extension = null)
    {
        return $this->adapter->addPermission($name,$title,$description,$extension);
    }

    /**
     * Resolve permission full name  name:type
     *
     * @param string $name
     * @return array
     */
    public function resolvePermissionName($name)
    {
        $tokens = explode(':',$name);
        $name = $tokens[0];
        $type = (isset($tokens[1]) == true) ? $tokens[1] : Self::FULL;     

        if (is_string($type) == true) {
            $type = (strtolower($type) == 'full') ? Self::FULL : Arrays::toArray($type,",");
        }
        
        return [$name,$type];
    }

    /**
     * Resolve permission type
     *
     * @param string|array $type
     * @return array|null
     */
    protected function resolvePermissionType($type)
    {
        if (is_array($type) == true) {
            return $type;
        }
    
        if (is_string($type) == true) {
            $type = Arrays::toArray($type,",");
        }

        return null;
    }
}   
