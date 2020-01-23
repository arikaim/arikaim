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
use Arikaim\Core\Access\Access;
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
     * User groups relation
     *
     * @return void
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
    public function verifyUserName($userName, $id) 
    {
        $model = $this->where("user_name","=",trim($userName))->first();

        if (is_object($model) == true) {
            return ($model->id == $id);
        } 
        
        return true;
    }

    /**
     * Return true if username exist
     *
     * @param string $userName    
     * @return boolean
     */
    public function hasUserName($userName) 
    {
        $model = $this->where("user_name","=",trim($userName))->first();

        return is_object($model);       
    }

    /**
     * Return true if email exist
     *
     * @param string $email    
     * @return boolean
     */
    public function hasUserEmail($email) 
    {
        $model = $this->where("email","=",trim($email))->first();

        return is_object($model);       
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @param integer $id
     * @return array|false
     */
    public function verifyEmail($email, $id) 
    {
        $model = $this->where("email","=",trim($email))->first();
        if (is_object($model) == true) {
            return ($model->id == $id);
        } 
        
        return true;  
    }

    /**
     * Set login date to current time
     *
     * @return boolean
     */
    public function updateLoginDate()
    {
        $this->date_login = DateTime::getTimestamp();

        return $this->save();
    }

    /**
     * Get user by credentails
     *
     * @param array $credentials
     * @return Model|false
     */
    public function getUserByCredentials(array $credentials)
    {
        $user = $this->where('status','=',$this->ACTIVE());

        if (isset($credentials['user_name']) == true) {
            $user = $user->where('user_name','=',$credentials['user_name'])->whereNotNull('user_name');    
            if (is_object($user->first()) == false) {
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
      
        return (is_object($user) == false) ? false : $user;
    }

    /**
     * Return user details by auth id
     *
     * @param string|integer $id
     * @return array|false
     */
    public function getUserById($id)
    {
        $model = $this->findById($id);

        return (is_object($model) == true) ? $model->toArray() : false;
    }

    /**
     * Get user with control panel permission
     *
     * @return Model|false
     */
    public function getControlPanelUser()
    {
        $permisisonId = DbModel::Permissions()->getId(Access::CONTROL_PANEL);
        if ($permisisonId == false) {
            return false;
        }
        
        $model = DbModel::PermissionRelations();

        $model = $model->where('permission_id','=',$permisisonId)->where('relation_type','=','user')->first();
        if (is_object($model) == false) {
            return false;
        }

        return $this->findById($model->relation_id);  
    }

    /**
     * Return true if user have control panel permission
     * 
     * @param integer|null $id 
     * @return boolean
     */
    public function isControlPanelUser($id = null)
    {
        $id = (empty($id) == true) ? $this->id : $id;
        $permisisonId = DbModel::Permissions()->getId(Access::CONTROL_PANEL);
        if ($permisisonId == false) {
            return false;
        }
        $model = DbModel::PermissionRelations()->getRelationsQuery($permisisonId,'user');
        $model = $model->where('relation_id','=',$id);

        if (is_object($model) == false) {
            return false;
        }

        return is_object($model);
    }

    /**
     * Return true if admin user exist
     *
     * @return boolean
     */
    public function hasControlPanelUser() 
    {
        return is_object($this->getControlPanelUser());
    }

    /**
     * Find user by user name or email
     *
     * @param string|null $userName
     * @param string|null $email
     * @return Model|false
     */
    private function getUser($userName, $email = null)
    {       
        if (empty($userName) == false) {
            $model = $this->where('user_name','=',$userName)->first();
            if (is_object($model) == true) {
                return $model;
            }
        }
        if (empty($email) == false) {
            $model = $this->where('email','=',$email)->first();
            if (is_object($model) == true) {
                return $model;
            }
        }
        
        return false;
    }

    /**
     * Create user
     *
     * @param string $userName
     * @param string $password
     * @param string|null $email
     * @return Model|false
     */
    public function createUser($userName, $password, $email = null)
    {
        $user = $this->getUser($userName,$email);
        if (is_object($user) == true) {           
            return false;
        }
        $data = [
            'uuid'          => UuidCreate::create(),
            'date_created'  => DateTime::getTimestamp(),
            'user_name'     => $userName,
            'status'        => 1,
            'password'      => $this->EncryptPassword($password),
            'email'         => $email
        ];
   
        return $this->create($data);
    }
}
