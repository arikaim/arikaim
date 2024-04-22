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
use Arikaim\Core\Utils\DateTime;
use Arikaim\Core\Utils\Uuid as UuidCreate;
use Arikaim\Core\Interfaces\Access\AccessInterface;
use Arikaim\Core\Access\Interfaces\UserProviderInterface;
use Arikaim\Core\Db\Model as DbModel;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\DateCreated;
use Arikaim\Core\Db\Traits\SoftDelete;
use Arikaim\Core\Access\Traits\Auth;
use Arikaim\Core\Access\Traits\Password;

/**
 * Users database model
*/
class Users extends Model implements UserProviderInterface
{
    use Uuid,
        Find,
        Status,
        DateCreated,
        SoftDelete,
        Auth,
        Password;     

    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [
        'uuid',
        'user_name',
        'email',
        'status',
        'password',      
        'date_login',
        'date_created',       
        'date_deleted'
    ];

    /**
     * Hidden attributes
     *
     * @var array
     */
    protected $hidden = ['password'];

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
    protected $table = 'users';

    /**
     * User details relation
     *
     * @return Relation|null
    */
    public function details()
    {
        return $this->hasOne('Arikaim\\Extensions\\Users\\Models\\UserDetails','user_id');     
    }

    /**
     * User groups relation
     *
     * @return Relation
     */
    public function groups()
    {
        return $this->hasMany(UserGroupMembers::class,'user_id','id');     
    }

    /**
     * Verify username
     *
     * @param string $userName
     * @param integer $id
     * @return boolean
     */
    public function verifyUserName(string $userName, int $id): bool 
    {
        $model = $this->where('user_name','=',\trim($userName))->first();

        return ($model == null) ? true : ($model->id == $id);
    }

    /**
     * Return true if username exist
     *
     * @param string $userName    
     * @return boolean
     */
    public function hasUserName(string $userName): bool 
    {
        $model = $this->where('user_name','=',\trim($userName))->first();

        return ($model != null);       
    }

    /**
     * Return true if email exist
     *
     * @param string $email    
     * @return boolean
     */
    public function hasUserEmail(string $email): bool 
    {
        $model = $this->where('email','=',\trim($email))->first();

        return ($model != null);       
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @param integer $id
     * @return array|false
     */
    public function verifyEmail(string $email, int $id): bool 
    {
        $model = $this->where('email','=',\trim($email))->first();

        return ($model == null) ? true : ($model->id == $id); 
    }

    /**
     * Set login date to current time
     *
     * @return boolean
     */
    public function updateLoginDate(): bool
    {
        $this->date_login = DateTime::getTimestamp();

        return (bool)$this->save();
    }

    /**
     * Get user by credentails
     *
     * @param array $credentials
     * @return array|null
     */
    public function getUserByCredentials(array $credentials): ?array
    {
        $password = $credentials['password'] ?? null;
        if (empty($password) == true) {
            return null;
        }

        $user = $this->where('status','=',$this->ACTIVE());

        if (isset($credentials['user_name']) == true) {
            $user = $user->where('user_name','=',$credentials['user_name'])->whereNotNull('user_name');    
            if ($user->first() == null) {
                // try with email
                $user = $user->where('email','=',$credentials['user_name'])->whereNotNull('email');
            }               
        }
        if (isset($credentials['email']) == true) {
            $user = $user->where('email','=',$credentials['email'])->whereNotNull('email');           
        }
        // by id or uuid
        if (isset($credentials['id']) == true) {
            $user = $user->where('id','=',$credentials['id']);
            $user = $user->orWhere('uuid','=',$credentials['id']);
        }
        $user = $user->first();
        if ($user == null) {
            return null;
        }
        // check if soft deleted 
        if ($user->isDeleted() == true) {
            return null;
        }
        
        // check password
        if ($user->verifyPassword($password) == false) {
            return null;
        }
        $authId = $user->getAuthId();
        $user = $user->toArray();
        $user['auth_id'] = $authId;

        return $user;
    }

    /**
     * Return user details by auth id
     *
     * @param string|integer $id
     * @return array|null
     */
    public function getUserById($id): ?array
    {
        if (empty($id) == true) {
            return null;
        }

        $model = $this->findById($id);
        if ($model == null) {
            return null;
        }
        $user = $model->toArray();
        $user['auth_id'] = $model->getAuthId();

        return $user;
    }

    /**
     * Get user with control panel permission
     *
     * @return Model|null
     */
    public function getControlPanelUser(): ?object
    {
        $permisisonId = DbModel::Permissions()->getId(AccessInterface::CONTROL_PANEL);
        if ($permisisonId == false) {
            return null;
        }
        
        $model = DbModel::PermissionRelations();
        $model = $model->where('permission_id','=',$permisisonId)->where('relation_type','=','user')->first();
        
        return ($model == null) ? null : $this->findById($model->relation_id);   
    }

    /**
     * Return true if user have control panel permission
     * 
     * @param integer|null $id 
     * @return boolean
     */
    public function isControlPanelUser($id = null): bool
    {
        $id = $id ?? $this->id;
        $permisisonId = DbModel::Permissions()->getId(AccessInterface::CONTROL_PANEL);
        if ($permisisonId == false) {
            return false;
        }
        $model = DbModel::PermissionRelations()->getRelationsQuery($permisisonId,'user');
        $model = $model->where('relation_id','=',$id)->first();

        return ($model != null);
    }

    /**
     * Return true if admin user exist
     *
     * @return boolean
     */
    public function hasControlPanelUser(): bool 
    {
        return ($this->getControlPanelUser() != null);
    }

    /**
     * Find user by user name or email
     *
     * @param string|null $userName
     * @param string|null $email
     * @return Model|null
     */
    public function getUser(?string $userName, ?string $email = null)
    {       
        if (empty($userName) == false) {
            $model = $this->where('user_name','=',$userName)->first();
            if ($model != null) {
                return $model;
            }
        }
        if (empty($email) == false) {
            $model = $this->where('email','=',$email)->first();
            if ($model != null) {
                return $model;
            }
        }
        
        return null;
    }

    /**
     * Create user
     *
     * @param string|string $userName
     * @param string $password
     * @param string|null $email
     * @return Model|false
     */
    public function createUser(?string $userName, string $password, ?string $email = null)
    {
        $user = $this->getUser($userName,$email);
        if ($user != null) {           
            return false;
        }
        if (empty($userName) == true && empty($email) == true) {
            return false;
        }
        
        return $this->create([
            'uuid'          => UuidCreate::create(),
            'date_created'  => DateTime::getTimestamp(),
            'user_name'     => (empty($userName) == true) ? null : $userName,
            'status'        => 1,
            'password'      => $this->encryptPassword($password),
            'email'         => $email
        ]);
    }

    /**
     * Get user_name_or_email attribute
     *
     * @return string
     */
    public function getUserNameOrEmailAttribute()
    {
        return (empty($this->user_name) == true) ? $this->email : $this->user_name;
    }

    /**
     * Hard delete user
     *
     * @return boolean
     */
    public function deleteUser(): bool
    {
        // remove user form groups
        $members = DbModel::UserGroupMembers();      
        $members->where('user_id','=',$this->id)->delete();

        // remove user permissions
        $permissions = DbModel::PermissionRelations();
        $permissions = $permissions->getRelationsQuery($this->id,'user');    
        $permissions->delete();

        return (bool)$this->delete();
    }

    /**
     * Find user by username or email
     *
     * @param string $userName
     * @return Model|null
     */
    public function findUser(string $userName)
    {
        $user = $this->where('user_name','=',$userName)->whereNotNull('user_name')->first();    
        if ($user == null) {
            // try with email
            $user = $this->where('email','=',$userName)->whereNotNull('email')->first();
        } 
        if ($user == null) {
            // try with uuid or id 
            $user = $this->where('uuid','=',$userName)->orWhere('id','=',$userName)->first();
        }

        return $user;
    }
}
