<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits\Permissions;

/**
 * Permissions
*/
trait Permissions 
{    
    /**
     * Return true if have permission 
     *
     * @param string $name valid values read|write|delete|execute
     * @param bool $deny
     * @return boolean
     */
    public function hasPermission(string $name, bool $deny = false): bool
    {
        $permission = $this->attributes[$name] ?? null;
       
        return ($deny == true) ? ($permission != 1) : ($permission == 1);
    }

    /**
     * Check for permissions
     *
     * @param array $permissions
     * @param bool $deny
     * @return boolean
     */
    public function verifyPermissions(array $permissions, bool $deny = false): bool
    {
        foreach ($permissions as $key => $value) {
            $success = ($value == 1) ? $this->hasPermission($key,$deny) : true;
            if ($success == false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return true if have all permissions
     *
     * @param bool $deny
     * @return boolean
     */
    public function hasFull(bool $deny = false): bool
    {
        $count = 0;
        $count += ($this->hasPermission('read',$deny) == false) ? 0 : 1;
        $count += ($this->hasPermission('write',$deny) == false) ? 0 : 1;
        $count += ($this->hasPermission('delete',$deny) == false) ? 0 : 1;
        $count += ($this->hasPermission('execute',$deny) == false) ? 0 : 1;

        return ($count == 4);
    }

    /**
     * Resolve permissions array
     *
     * @param array|string $access
     * @return array
     */
    public function resolvePermissions($access): array 
    {
        if (\is_string($access) == true) {
            $access = \strtolower(\trim($access));
            $access = ($access == 'full') ? ['read','write','delete','execute'] : \explode(',',$access);
        }

        return [
            'read'    => \in_array('read',$access) ? 1 : 0,
            'write'   => \in_array('write',$access) ? 1 : 0,
            'delete'  => \in_array('delete',$access) ? 1 : 0,
            'execute' => \in_array('execute',$access) ? 1 : 0
        ];       
    }

    /**
     * Add permission type
     *
     * @param string $permissionType
     * @param string|null|int $id
     * @return boolean
     */
    public function addPermisionType(string $permissionType, $id = null): bool
    {
        $model = (empty($id) == true) ? $this : $this->findById($id);
        if ($model == null) {
            return false;
        }
        $result = $model->update([
            $permissionType => 1
        ]);

        return ($result !== false);
    }

    /**
     * Remove permission type
     *
     * @param string $permissionType
     * @param string|null|int $id
     * @return boolean
     */
    public function removePermisionType(string $permissionType, $id = null): bool
    {
        $model = (empty($id) == true) ? $this : $this->findById($id);
        if ($model == null) {
            return false;
        }
        $result = $model->update([
            $permissionType => 0
        ]);

        return ($result !== false);
    }
}
