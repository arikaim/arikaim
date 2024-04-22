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
use Arikaim\Core\Models\UserGroupMembers;

use Arikaim\Core\Db\Model as DbModel;
use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\Slug;

/**
 * User groups database model
 */
class UserGroups extends Model  
{
    use Uuid,
        Slug,
        Status,
        Find;

    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [        
        'title',
        'uuid',
        'slug',
        'status',
        'description'
    ];

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'user_groups';

    /**
     * Find group model by id, uuid, slug, title
     *
     * @param mixed $value
     * @return Model|null
     */
    public function findGroup($value): ?object
    {
        return $this->findByColumn($value,['id','uuid','slug','title']);
    }

    /**
     * Group members relation
     *
     * @return Relation
     */
    public function members()
    {
        return $this->hasMany(UserGroupMembers::class,'group_id');
    }

    /**
     * Return true if user is member in current group.
     *
     * @param integer $userId
     * @param object|null $group
     * @return boolean
     */
    public function hasUser(int $userId, $group = null): bool
    {
        $group = (\is_object($group) == false) ? $this : $group;
        
        return ($group->members()->where('user_id','=',$userId)->first() !== null);       
    }

    /**
     * Return true if user is member of gorup 
     *
     * @param integer|string $group  Group Id, Uuid or Slug
     * @param integer $userId
     * @return bool
     */
    public function inGroup($group, int $userId): bool
    {
        $group = $this->findGroup($group);
      
        return ($group != null) ? $this->hasUser($userId,$group) : false;         
    }

    /**
     * Get user groups
     *
     * @param integer $userId
     * @return array
     */
    public function getUserGroups(int $userId): array
    {
        $model = DbModel::UserGroupMembers()->where('user_id','=',$userId)->get();

        return ($model == null) ? [] : $model->toArray();
    }

    /**
     * Add user to group
     *
     * @param integer|string $group Id, Uuid, slug or title
     * @param integer|string $userId
     * @param integer|null $dateExpire
     * @return bool
     */
    public function addUser($group, $userId, ?int $dateExpire = null): bool
    {
        if (empty($group) == true || empty($userId) == true) {
            return false;
        }
        
        $user = DbModel::Users()->findById($userId);
        if ($user == null) {
            return false;
        }

        $group = $this->findGroup($group);
        if ($group == null) {
            return false;
        }

        if ($this->hasUser($user->id,$group) == true) {
            return true;
        }

        $model = DbModel::UserGroupMembers()->create([
            'group_id'     => $group->id,
            'user_id'      => $userId,
            'date_expired' => $dateExpire
        ]);

        return ($model != null);
    }

    /**
     * Remove user from group
     *
     * @param integer $groupId
     * @param integer $userId
     * @return bool
     */
    public function removeUser(int $groupId, int $userId): bool
    {       
        $model = $this->members()->where('group_id','=',$groupId);
        $model = $model->where('user_id','=',$userId);
        
        return (bool)$model->delete();
    }

    /**
     * Deleet user form all groups
     *
     * @param int $userId
     * @return bool
     */
    public function deleteUser(int $userId): bool
    {
        $model = $this->members()->where('user_id','=',$userId);

        return (\is_object($model) == true) ? (bool)$model->delete() : true;
    }

    /**
     * Create group
     *
     * @param string $title
     * @param string $description
     * @return Model|false
     */
    public function createGroup(string $title, string $description = '')
    {
        if ($this->findByColumn($title,'title') != null) {
            // group exist
            return false;
        }
           
        return $this->create([ 
            'title'       => $title, 
            'description' => $description            
        ]);       
    }
}
