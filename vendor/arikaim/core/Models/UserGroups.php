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
use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Models\UserGroupMembers;

/**
 * User groups database model
 */
class UserGroups extends Model  
{
    use Uuid,
        Find;

    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [        
        'title',
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
     * Group members relation
     *
     * @return UserGroupMembers
     */
    public function members()
    {
        return $this->hasMany(UserGroupMembers::class);
    }

    /**
     * Return true if user is member in current group.
     *
     * @param integer $userId
     * @param object|null $model
     * @return boolean
     */
    public function hasUser($userId, $model = null)
    {
        $model = (is_object($model) == false) ? $this : $model;
        $model = $model->members()->where('user_id','=',$userId)->first();

        return is_object($model);
    }

    /**
     * Return true if user is member of gorup 
     *
     * @param integer $groupId
     * @param integer $userId
     * @return bool
     */
    public function inGroup($groupId, $userId)
    {
        $model = $this->where('id','=',$groupId)->get();

        return (is_object($model) == true) ? $this->hasUser($userId,$model) : false;         
    }

    /**
     * Get user groups
     *
     * @param integer $userId
     * @return Model
     */
    public function getUserGroups($userId)
    {
        $model = UserGroupMembers::where('user_id','=',$userId)->get();

        return (is_object($model) == true) ? $model : [];
    }

    /**
     * Add user to group
     *
     * @param integer $groupId
     * @param integer|string $userId
     * @param integer|null $dateExpire
     * @return bool
     */
    public function addUser($groupId, $userId, $dateExpire = null)
    {
        if ($this->findById($userId) == true) {
            return true;
        }

        $info = [
            'group_id'    => $groupId,
            'user_id'     => $userId,
            'date_expire' => $dateExpire
        ];
        $model = UserGroupMembers::create($info);

        return is_object($model);
    }

    /**
     * Remove user from group
     *
     * @param integer $groupId
     * @param integer $userId
     * @return bool
     */
    public function removeUser($groupId, $userId)
    {       
        $model = $this->members()->where('group_id','=',$groupId);
        $model = $model->where('user_id','=',$userId);
        
        return $model->delete();
    }
}
