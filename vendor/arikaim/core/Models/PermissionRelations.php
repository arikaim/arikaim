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
use Arikaim\Core\Arikaim;
use Arikaim\Core\Models\Permissions;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\PolymorphicRelations;

/**
 * Permissions database model
 */
class PermissionRelations extends Model implements PermissionsInterface
{
    const USER = 'user';
    const GROUP = 'group';

    use Uuid,
        PolymorphicRelations,
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
     * Set user permission
     *
     * @param string $name
     * @param array $permissions
     * @param integer|string|null $id
     * @return bool
     */
    public function setUserPermission($name, $permissions, $id = null) 
    {
        if (is_string($id) == true) {
            $model = DbModel::Users()->findById($id);
            $id = (is_object($model) == true) ? $model->id : null; 
        }
        return $this->setPermission($name,$permissions,$id,Self::USER);
    }
    
    /**
     * Set group permisison
     *
     * @param string $name
     * @param array $permissions
     * @param integer|string $id
     * @return bool
     */
    public function setGroupPermission($name, $permissions, $id)
    {
        if (is_string($id) == true) {
            $model = DbModel::UserGroups()->findById($id);
            $id = (is_object($model) == true) ? $model->id : null;
        }
        return $this->setPermission($name,$permissions,$id,Self::GROUP);
    }

    /**
     * Get user permission
     *
     * @param string $name
     * @param integer|null $id
     * @return Model|false
     */
    public function getUserPermission($name, $id = null)
    {
        if (is_string($id) == true) {
            $model = DbModel::Users()->findById($id);
            $id = (is_object($model) == true) ? $model->id : null; 
        }
        // check for user permiission
        $permission = $this->getPermission($name,$id,Self::USER);
        if (is_object($permission) == true) {
            return $permission;
        }

        // check groups
        $groupList = DbModel::UserGroups()->getUserGroups($id);
        foreach ($groupList as $group) {
            $permission = $this->getGroupPermission($name,$group->id);
            if (is_object($permission) == true) {
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
        if (is_string($id) == true) {
            $model = DbModel::UserGroups()->findById($id);
            $id = (is_object($model) == true) ? $model->id : null;
        }
      
        return (is_object($model) == true) ? $this->getPermission($name,$id,Self::GROUP) : false;      
    }

    /**
     * Return permission for user or group
     *
     * @param string|integer $name
     * @param integer|null $id
     * @param integer $type
     * @return Model|bool
     */
    public function getPermission($name, $id = null, $type = Self::USER)
    {
        if (Schema::hasTable($this) == false) {          
            return false;
        }
        $id = ($id == null && $type == Self::USER) ? Arikaim::access()->getId() : $id;
        $permissionId = (is_string($name) == true) ? DbModel::Permissions()->getId($name) : $name;

        $query = $this->getRelationsQuery($permissionId,$type);
        $model = $query->first();

        return (is_object($model) == true) ? $model : false;           
    }

    /**
     * Add or Update permission 
     *
     * @param string|integer $name
     * @param array $access - ['read','write','delete','execute]
     * @param integer|null $id user Id or group Id 
     * @param integer $type
     * @return bool
     */
    public function setPermission($name, $access, $id = null, $type = Self::USER) 
    {
        $permissions = $this->resolvePermissions($access); 
        $id = ($id == null && $type == Self::USER) ? Arikaim::access()->getId() : $id;
        $relationId = DbModel::Permissions()->getId($name);       
        if (empty($relationId) == true) {
            return false;
        }
        $model = $this->saveRelation($id, $type, $relationId);
        $result = (is_object($model) == true) ? $model->update($permissions) : false;        

        return $result;
    }

    /**
     * Resolve permissions array
     *
     * @param array $access
     * @return array
     */
    public function resolvePermissions(array $access) 
    {
        return [
            'read'      => in_array('read',$access) ? 1:0,
            'write'     => in_array('write',$access) ? 1:0,
            'delete'    => in_array('delete',$access) ? 1:0,
            'execute'   => in_array('execute',$access) ? 1:0
        ];       
    }

    /**
     * Check for permissions in current object
     *
     * @param array $permissions
     * @param string name
     * @param mixed id
     * @return boolean
     */
    public function hasPermissions($name, $id, $permissions)
    {
        if (is_array($permissions) == false || count($permissions) == 0) {
            return false;
        } 
        $model = $this->getUserPermission($name,$id);       
        if (is_object($model) == false) {
            return false;
        }
    
        foreach ($permissions as $permission) {               
            if ($model->hasPermission($permission) == false) {              
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
     * @return boolean
     */
    public function addPermission($name, $title = null, $description = null, $extension = null)
    {
        $model = DbModel::Permissions();

        if ($model->has($name) == true) {
            return false;
        }
        $item = [
            'name'           => $name,
            'extension_name' => $extension,
            'title'          => $title,
            'description'    => $description
        ];
        $permission = $model->create($item);

        return is_object($permission);
    }

    /**
     * Return true if have permission 
     *
     * @param string $name valid values read|write|delete|execute
     * @return boolean
     */
    public function hasPermission($name)
    {
        if (isset($this->attributes[$name]) == true) {
            return ($this->attributes[$name] == 1) ? true : false;
        }

        return false;
    }

    /**
     *Return true if have all permissions
     *
     * @return boolean
     */
    public function hasFull()
    {
        $count = 0;
        $count += ($this->hasPermission('read') == false) ? 0 : 1;
        $count += ($this->hasPermission('write') == false) ? 0 : 1;
        $count += ($this->hasPermission('delete') == false) ? 0 : 1;
        $count += ($this->hasPermission('execute') == false) ? 0 : 1;
        return ($count == 4) ? true : false;
    }
}
