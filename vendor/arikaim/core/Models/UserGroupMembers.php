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

use Arikaim\Core\Models\Users;
use Arikaim\Core\Models\UserGroups;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\DateCreated;

/**
 * User groups details database model
 */
class UserGroupMembers extends Model  
{
    use Uuid,
        Find,
        DateCreated;

    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [        
        'user_id',
        'group_id',
        'date_expired',
        'date_created'
    ];
    
    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'user_group_members';

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * User group relation
     *
     * @return Relation|null
     */
    public function group()
    {
        return $this->belongsTo(UserGroups::class,'group_id');     
    }

    /**
     * User relation
     *
     * @return Relation|null
     */
    public function user()
    {
        return $this->belongsTo(Users::class,'user_id');     
    }

    /**
     * Add member to group
     *
     * @param string|integer $userId  Group Id or Group slug
     * @param string|integer $groupId
     * @return Model|false
     */
    public function addMember($userId, $groupId)
    {
        $user = new Users();
        $user = $user->findById($userId);
        if ($user == null) {
            // not valid user id
            return false;
        }

        $groupModel = new UserGroups();
        $group = $groupModel->findById($groupId);
        if ($group == null) {
            $group = $groupModel->findBySlug($groupId);           
        }

        if ($group == null) {
            // not vlaid group id or slug
            return false;      
        }

        if ($this->isMember($user->id,$group->id) == true) {
            return false;
        }
        
        $member = $this->create([
            'user_id'  => $user->id,
            'group_id' => $group->id
        ]);

        return ($member != null) ? $member : false;
    }

    /**
     * Add member to group
     *
     * @param integer $userId  Group Id or Group slug
     * @param integer $groupId
     * @return boolean
    */
    public function isMember(int $userId, int $groupId): bool
    {
        return ($this->where('user_id','=',$userId)->where('group_id','=',$groupId)->first() != null);
    }

    /**
     * User goroups scope
     *
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeUserGroups($query, int $userId)
    {
        return $query->where('user_id','=',$userId);
    } 
}
