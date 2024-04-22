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

use Arikaim\Core\Models\Permissions;
use Arikaim\Core\Models\UserGroupMembers;

/**
 * Entity permissions
*/
trait EntityPermissions 
{    
    /**
     * Get entity relation
     *
     * @return Relation|null
     */
    public function entity()
    {      
        return $this->belongsTo($this->entytyModelClass,'entity_id');
    }

    /**
     * Get permission relation
     *
     * @return Relation|null
     */
    public function permission()
    {      
        return $this->belongsTo(Permissions::class,'permission_id');
    }

    /**
     * Get permission name attribute
     *
     * @return string
     */
    public function getNameAttribute()
    {
        $permission = $this->permission();

        return (\is_object($permission) == true) ? $permission->name : '';
    }

    /**
     * Morphed models
     *     
     * @return Relation|null
     */
    public function related()
    {       
        return $this->morphTo('relation');          
    }

    /**
     * Delete user permission
     *
     * @param integer $entityId
     * @param integer $userId
     * @return boolean
     */
    public function deleteUserPermission(int $entityId, int $userId): bool
    {
        $model = $this->getPermission($entityId,$userId,'user');

        return ($model != null) ? (bool)$model->delete() : true;
    }
    
    /**
     * Delete group permission
     *
     * @param integer $entityId
     * @param integer $groupId
     * @return boolean
     */
    public function deleteGroupPermission(int $entityId, int $groupId): bool
    {
        $model = $this->getPermission($entityId,$groupId,'group');

        return ($model != null) ? (bool)$model->delete() : true;
    }

    /**
     * Add user permission
     *
     * @param integer $entityId
     * @param integer $userId
     * @param array|string $permissions
     * @param integer|null $permissionId
     * @return Model|false
     */
    public function addUserPermission(int $entityId, int $userId, $permissions, ?int $permissionId = null)
    {
        return $this->addPermission($entityId, $userId, $permissions,'user',$permissionId);
    }

    /**
     * Add group permission
     *
     * @param integer $entityId
     * @param integer $groupId
     * @param array|string $permissions
     * @param integer|null $permissionId
     * @return Model|false
     */
    public function addGroupPermission(int $entityId, int $groupId, $permissions, ?int $permissionId = null)
    {
        return $this->addPermission($entityId,$groupId,$permissions,'group',$permissionId);
    }

    /**
     * Add permission
     *
     * @param integer $entityId
     * @param integer $id
     * @param array|string $permissions
     * @param string $type  (user or gorup)
     * @param integer|null $permissionId
     * @return Model|false
     */
    public function addPermission(int $entityId, int $id, $permissions, string $type = 'user', ?int $permissionId = null)
    {
        $permissions = $this->resolvePermissions($permissions);
        $model = $this->getPermission($entityId,$id,$type);
        if ($model !== null) {
            return false;
        }

        $permissions['entity_id'] = $entityId;
        $permissions['relation_id'] = $id;
        $permissions['relation_type'] = $type;
        $permissions['permission_id'] = $permissionId;
        
        return $this->create($permissions);
    }

    /**
     * Add public permission
     *
     * @param integer $entityId
     * @param array|string $permissions
     * @return Model
     */
    public function addPublicPermission(int $entityId, $permissions)
    { 
        $model = $this->getPublicPermission($entityId);
        if ($model != null) {
            return false;
        }
        $permissions = $this->resolvePermissions($permissions);
        $permissions['entity_id'] = $entityId;
        $permissions['relation_id'] = null;
        $permissions['relation_type'] = 'user';
    
        return $this->create($permissions);          
    }

    /**
     * Get public permission
     *
     * @param integer $entityId
     * @return Model|null
     */
    public function getPublicPermission(int $entityId): ?object
    {
        return $this
            ->where('entity_id','=',$entityId)
            ->whereNull('relation_id')
            ->where('relation_type','=','user')->first();       
    }
    
    /**
     * Delete public permissions
     *
     * @param integer $entityId
     * @return boolean
     */
    public function deletePublicPermission(int $entityId): bool
    {
        $model = $this->getPublicPermission($entityId);

        return ($model != null) ? (bool)$model->delete() : true;
    }

    /**
     * Get permission model
     *
     * @param integer $entityId
     * @param integer $id
     * @param string $type
     * @return Model|null
     */
    public function getPermission(int $entityId, int $id, string $type = 'user'): ?object
    {
        return $this
            ->where('entity_id','=',$entityId)
            ->where('relation_id','=',$id)
            ->where('relation_type','=',$type)->first();       
    }

    /**
     * Get permissions query 
     *
     * @param int $entityId
     * @param string|null $type
     * @return Builder
     */
    public function getPermissionsQuery(int $entityId, ?string $type = null)
    {
        $query = $this->where('entity_id','=',$entityId);
        if (empty($type) == false) {
            $query->where('relation_type','=',$type);
        }

        return $query->whereNotNull('relation_id');      
    }

    /**
     * Permissions scope query
     *
     * @param Builder      $query
     * @param integer|null $entityId
     * @param string|null  $type
     * @param integer|null $typeId
     * @return Builder
     */
    public function scopePermissionsQuery($query, ?int $entityId = null, ?string $type = null, ?int $typeId = null)
    {
        if (empty($entityId) == false) {
            $query->where('entity_id','=',$entityId);
        }

        if (empty($type) == false) {
            $query->where('relation_type','=',$type);
        }

        if (empty($typeId) == false) {
            $query->where('relation_id','=',$typeId);
        }

        return $query;
    }

    /**
     * Query for all permissions for user
     *
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopePermissionsForUser($query, int $userId)
    {
        $groups = new UserGroupMembers();
        $groups = $groups->userGroups($userId)->pluck('group_id')->toArray();
        
        $query = $query->where(function($query) use ($userId) {
            // user
            $query->where('relation_id','=',$userId);
            $query->where('relation_type','=','user');
        })->orWhere(function($query) use ($groups) {
            // groups
            $query->whereIn('relation_id',$groups);
            $query->where('relation_type','=','group');
        })->orWhere(function($query) {
            // public
            $query->whereNull('relation_id');           
        });

        return $query;
    }

    /**
     * Get user permissions query
     *
     * @param Builder $query
     * @return Builder
    */
    public function scopeUserPermissions($query)
    {
        return $query->where('relation_type','=','user');
    } 

    /**
     * Get group permissions query
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeGroupPermissions($query)
    {
        return $query->where('relation_type','=','group');
    } 
}
