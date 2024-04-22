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

use Arikaim\Core\Access\Interfaces\UserProviderInterface;
use Arikaim\Core\Access\Interfaces\AuthTokensInterface;
use Arikaim\Core\Utils\Text;
use Arikaim\Core\Utils\Uuid as UuidFactory;
use Arikaim\Core\Utils\DateTime;
use Arikaim\Core\Models\Users;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\DateCreated;
use Arikaim\Core\Access\Traits\Auth;

/**
 * Access tokens database model
*/
class AccessTokens extends Model implements UserProviderInterface
{
    use Uuid,
        Find,
        Status,
        Auth,
        DateCreated;

    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'access_tokens';

    /**
     * Auth id column name
     *
     * @var string
     */
    protected $authIdColumn = 'user_id';

    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [
        'token',
        'date_expired',
        'user_id',
        'type'
    ];

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

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
     * Get token type
     *
     * @param string $token
     * @return integer|null
     */
    public function getType(string $token): ?int
    {
        $model = $this->getToken($token);
        
        return ($model == null) ? null : $model->type;
    }

    /**
     * Get user credentials
     *
     * @param array $credential
     * @return array|null
     */
    public function getUserByCredentials(array $credentials): ?array
    {
        $token = $credentials['token'] ?? null;
        if (empty($token) == true) {
            return null;
        }
      
        $model = $this->findByColumn($token,'token');
        if ($model == null) {
            return null;
        }
        if ($model->isExpired() == true) {  
            // token expired                 
            return null;
        }
        if ($model->status != $this->ACTIVE()) { 
            // token is disabled
            return null;
        }

        $user = $model->user()->first();

        if ($user->status != 1) {
            // user not active
            return null;
        }

        if ($user->isDeleted() == true) {
            // user is deleted
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

        return ($model == null) ? null : $model->user()->first()->toArray();          
    }

    /**
     * Return true token is correct.
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        return ($this->findByColumn($password,'token') != null);
    }

    /**
     * Expired mutator attribute
     *
     * @return boolean
     */
    public function getExpiredAttribute()
    {
        return ($this->date_expired == -1) ? false : $this->isExpired();          
    }

    /**
     * Create access token
     *
     * @param integer $userId
     * @param integer $type
     * @param integer $expireTime
     * @param bool $deleteExpired
     * @return array|false
     */
    public function createToken(
        int $userId, 
        int $type = AuthTokensInterface::PAGE_ACCESS_TOKEN, 
        int $expireTime = 1800, 
        bool $deleteExpired = true
    )
    {       
        $dateExpired = ($expireTime != -1) ? DateTime::getTimestamp() + $expireTime : $expireTime;
        switch($type) {
            case AuthTokensInterface::LOGIN_ACCESS_TOKEN: 
                $token = UuidFactory::create();
                break;
            case AuthTokensInterface::PAGE_ACCESS_TOKEN:
                $token = UuidFactory::create();
                break;
            case AuthTokensInterface::API_ACCESS_TOKEN:
                $token = Text::createToken(62);
                break;
            default:
                $token = UuidFactory::create();
        }
       
        if ($type == AuthTokensInterface::PAGE_ACCESS_TOKEN) {
            $this->deleteUserToken($userId,$type);
        }
      
        if ($deleteExpired == true) {          
            $this->deleteExpired($userId,$type);
        }
        
        $model = $this->getTokenByUser($userId,$type);
        if ($model != null) {
            return $model->toArray();
        }

        $model = $this->create([
            'user_id'      => $userId,
            'token'        => $token,
            'date_expired' => $dateExpired,
            'type'         => $type
        ]);

        return $model->toArray();
    }

    /**
     * Remove access token
     *
     * @param string $token
     * @return boolean
     */
    public function removeToken(string $token): bool
    {
        $model = $this->findByColumn($token,['uuid','token']);

        return ($model != null) ? (bool)$model->delete() : true;           
    }

    /**
     * Get access token
     *
     * @param  string $token
     * @return Model|null
     */
    public function getToken(string $token)
    {      
        return $this->findByColumn($token,'token');
    }

    /**
     * Return true if token is expired
     *
     * @param string|null $token
     * @return boolean
     */
    public function isExpired(?string $token = null): bool
    {
        $model = (empty($token) == true) ? $this : $this->findByColumn($token,'token');
        if ($model == null) {
            return true;
        }
        if ($model->date_expired == -1) {
            return false;
        }

        return ((DateTime::getTimestamp() > $model->date_expired) || (empty($model->date_expired) == true));
    }

    /**
     * Find token
     *
     * @param integer $userId
     * @param integer $type
     * @return Model|null
     */
    public function getTokenByUser(int $userId, int $type = AuthTokensInterface::PAGE_ACCESS_TOKEN)
    {
        return $this->where('user_id','=',$userId)->where('type','=',$type)->first();       
    }

    /**
     * Return true if token exist
     *
     * @param integer $userId
     * @param integer $type
     * @return boolean
     */
    public function hasToken(int $userId, int $type = AuthTokensInterface::PAGE_ACCESS_TOKEN): bool
    {    
        return ($this->getTokenByUser($userId,$type) != null);
    }

    /**
     * Delete user token
     *
     * @param integer $userId
     * @param integer|null $type
     * @return boolean
     */
    public function deleteUserToken(int $userId, ?int $type = AuthTokensInterface::PAGE_ACCESS_TOKEN): bool
    {
        $model = $this->where('user_id','=', $userId);
        if (empty($type) == false) {
            $model = $model->where('type','=',$type);
        }
       
        return (bool)$model->delete();
    }

    /**
     * Delete expired token
     *
     * @param integer $userId
     * @param integer|null $type
     * @return boolean
     */
    public function deleteExpired(int $userId, ?int $type = null): bool
    {
        $model = $this
            ->where('date_expired','<',DateTime::getTimestamp())
            ->where('date_expired','<>',-1)
            ->where('user_id','=',$userId);
        
        if ($type != null) {
            $model = $model->where('type','=',$type);
        }

        return (bool)$model->delete();
    }

    /**
     * Delete all expired tokens
     *
     * @return bool
     */
    public function deleteExpiredTokens(): bool
    {
        return (bool)$this->where('date_expired','<',DateTime::getTimestamp())->where('date_expired','<>',-1)->delete();
    }

    /**
     * Get all tokens for user
     *
     * @param integer $userId
     * @return null|Collection
     */
    public function getUserTokens(int $userId)
    {
        return $this->where('user_id','=',$userId)->get();
    }
}
