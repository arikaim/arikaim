<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Access\Interfaces\PermissionsInterface;
use Arikaim\Core\Db\Schema;
use Arikaim\Core\Db\Model as DbModel;
use Arikaim\Core\Models\Permissions;
use Arikaim\Core\Utils\Uuid as UuidFactory;
use Arikaim\Core\Utils\Utils;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\PolymorphicRelations;
use Arikaim\Core\Db\Traits\Permissions\Permissions as PermissionsTrait;

/**
 * Permissions database model
 */
class PermissionRelations extends Model implements PermissionsInterface
{
    const USER = 'user';
    const GROUP = 'group';

    use Uuid,
        PolymorphicRelations,
        PermissionsTrait,
        Find;
 
    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [
        'uuid',
        'read',
        'write',
        'delete',
        'execute',
        'permission_id',       
        'relation_id',
        'relation_type'               
    ];
    
    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'permission_relations';

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;
    
     /**
     * Relation model class
     *
     * @var string
     */
    protected $relationModelClass = Permissions::class;

    /**
     * Reation column name
     *
     * @var string
     */
    protected $relationColumnName = 'permission_id';

    /**
     * Permissions model relation
     *
     * @return Permissions
     */
    public function permission()
    {
        return $this->belongsTo(Permissions::class,'permission_id');
    }

    /**
     * Get users permisssions
     *
     * @param integer $userId
     * @return mixed
     */
    public function getUserPermissions($userId)
    {
        $query = $this->getRelationsQuery($userId,'user');
      
        return $query->get();
    }

    /**
     * Get user group permisssions
     *
     * @param integer $groupId
     * @return mixed
     */
    public function getGroupPermissions($groupId)
    {
        $query = $this->getRelationsQuery($groupId,'group');
      
        return $query->get();
    }

    /**
     * Set user permission
     *
     * @param string $name
     * @param array $permissions
     * @param integer|string|null $id
     * @return Model|bool
     */
    public function setUserPermission($name, $permissions, $id = null) 
    {
        if (\is_string($id) == true) {
            $model = DbModel::Users()->findById($id);
            $id = ($model != null) ? $model->id : null; 
        }

        return $this->setPermission($name,$permissions,$id,Self::USER);
    }
    
    /**
     * Set group permisison
     *
     * @param string $name Permission Name Or Slug
     * @param array $permissions
     * @param integer|string $id Group Id, Uuid or Slug
     * @return Model|bool
     */
    public function setGroupPermission($name, $permissions, $id)
    {
        if (\is_string($id) == true) {
            $model = DbModel::UserGroups();
            $group = $model->findById($id);
            if ($group == null) {
                $group = $model->findBySlug($id);                
            }
            if ($group == null) {
                return false;
            }

            $id = $group->id;
        }
 
        return $this->setPermission($name,$permissions,$id,Self::GROUP);
    }

    /**
     * Get user permission
     *
     * @param string|integer $name Permission name, slug or id
     * @param integer|string $userId
     * @return Model|false
     */
    public function getUserPermission($name, $userId)
    {
        if (\is_string($userId) == true) {
            $model = DbModel::Users()->findById($userId);
            $userId = ($model != null) ? $model->id : null; 
        }
        if (empty($userId) == true) {
            return false;
        }

        // check for user permiission
        $permission = $this->getPermission($name,$userId,Self::USER);
        if ($permission !== false) {
            return $permission;
        }
        // check groups
        $groupList = DbModel::UserGroups()->getUserGroups($userId);
        foreach ($groupList as $item) {          
            $permission = $this->getGroupPermission($name,$item['group_id']);
            if ($permission !== false) {
                return $permission;
            }
        }

        return false;
    }

    /**
     * Get group permission
     *
     * @param string $name
     * @param string|integer $id
     * @return Model|bool
     */
    public function getGroupPermission($name, $id)
    {
        if (\is_string($id) == true) {
            $model = DbModel::UserGroups()->findById($id);
            if ($model == null) {
                return false;
            }
            $id = $model->id;        
        }
             
        return $this->getPermission($name,$id,Self::GROUP);
    }

    /**
     * Return permission for user or group
     *
     * @param string|integer $name Permission name, slug or id
     * @param integer $id
     * @param string $type
     * @return Model|bool
     */
    public function getPermission($name, $id, $type = Self::USER)
    {
        if (Schema::hasTable($this) == false) {          
            return false;
        }
        if (empty($id) == true) {
            return false;
        }
       
        $permissionId = (\is_string($name) == true) ? DbModel::Permissions()->getId($name) : $name;

        $query = $this->getRelationsQuery($id,$type);
        $query = $query->where('permission_id','=',$permissionId);
        $model = $query->first();

        return ($model != null) ? $model : false;           
    }

    /**
     * Delete permission for user or group
     *
     * @param string|integer $name Permission name, slug or id
     * @param integer $id
     * @param string $type
     * @return bool
     */
    public function deletePermission($name, int $id, string $type = Self::USER): bool
    {
        $model = $this->getPermission($name,$id,$type);
        $result = ($model === false) ? true : $model->delete();
           
        return ($result !== false);
    }

    /**
     * Add or Update permission 
     *
     * @param string|integer $name
     * @param array $access - ['read','write','delete','execute]
     * @param integer $id user Id or group Id 
     * @param integer $type
     * @return Model|false
     */
    public function setPermission($name, $access, $id, $type = Self::USER) 
    {
        $permissions = $this->resolvePermissions($access); 
        $permissionId = DbModel::Permissions()->getId($name);     
        if (empty($permissionId) == true) {
            return false;
        }
        $model = $this->saveRelation($permissionId,$type,$id);
    
        if (\is_object($model) == false) {
            $model = $this->getRelationModel($permissionId,$type,$id);           
        }        
        $result = $model->update($permissions);  

        return ($result === false) ? false : $model;
    }

    /**
     * Check for permissions in current object
     *
     * @param string|int name
     * @param mixed $userId
     * @param array $permissions
     * @param bool $deny
     * @return boolean
     */
    public function hasPermissions($name, $userId, array $permissions, bool $deny = false): bool
    {
        if (\count($permissions) == 0) {
            return false;
        } 
      
        $model = $this->getUserPermission($name,$userId);       
        if ($model === false) {
            return false;
        }
    
        foreach ($permissions as $permission) {               
            if ($model->hasPermission($permission,$deny) == false) {              
                return false;
            }
        }
     
        return true;
    }

    /**
     * Add permission item.
     *
     * @param string $name    
     * @param string|null $title
     * @param string|null $description
     * @param string|null $extension
     * @param bool|null $deny
     * @return boolean
     */
    public function addPermission(
        string $name, 
        ?string $title = null, 
        ?string $description = null, 
        ?string $extension = null,
        ?bool $deny = false
    ): bool
    {
        $model = DbModel::Permissions();
        $slug = Utils::slug($title);
        $item = [          
            'name'           => $name,
            'extension_name' => $extension,
            'title'          => $title,
            'slug'           => $slug,
            'description'    => $description,
            'deny'           => (int)$deny ?? false
        ];

        $permission = $model->findPermission($name);
        if ($permission == null) {
            // try with slug
            $permission = $model->findBySlug($slug);
        }

        if ($permission == null) {
            // try with slug
            $permission = $model->findBySlug(Utils::slug($name));
        }
        
        if ($permission != null) {
            $result = $permission->update($item);
            return ($result !== false);
        }
      
        $item['uuid'] = UuidFactory::create();
        
        return ($model->create($item) != null);
    }    
}
