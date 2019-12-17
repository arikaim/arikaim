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
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Utils\Uuid as UuidFactory;
use Arikaim\Core\Utils\DateTime;

use Arikaim\Core\Db\Traits\Uuid;

use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\DateCreated;
use Arikaim\Core\Db\Traits\UserRelation;
use Arikaim\Core\Access\Traits\Auth;

/**
 * Access tokens database model
*/
class AccessTokens extends Model implements UserProviderInterface
{
    /**
     * Token access type
     */
    const PAGE_ACCESS_TOKEN  = 0;
    const LOGIN_ACCESS_TOKEN = 1;
    const API_ACCESS_TOKEN   = 2;

    use Uuid,
        Find,
        Auth,
        UserRelation,
        DateCreated;

    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'access_tokens';

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
     * Get user credentials
     *
     * @param array $credential
     * @return mixed|false
     */
    public function getUserByCredentials(array $credentials)
    {
        $token = (isset($credentials['token']) == true) ? $credentials['token'] : null;
        
        if (empty($token) == true) {
            return false;
        }
        if ($this->isExpired($token) == true) {
            return false;
        }

        $model = $this->findByColumn($token,'token');

        return is_object($model) ? $model->user() : false;
    }

    /**
     * Return true token is correct.
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        $model = $this->findByColumn($password,'token'); 

        return is_object($model);
    }

    /**
     * Expired mutator attribute
     *
     * @return void
     */
    public function getExpiredAttribute()
    {
        if ($this->date_expired == -1) {
            return false;
        }
        return (DateTime::getTimestamp() > $this->date_expired || empty($this->date_expired) == true) ? true : false;
    }

    /**
     * Create access token
     *
     * @param integer $userId
     * @param integer $type
     * @param integer $expire_period
     * @return Model|false
     */
    public function createToken($userId, $type = AccessTokens::PAGE_ACCESS_TOKEN, $expireTime = 1800, $deleteExpired = true)
    {
        $expireTime = ($expireTime < 1000) ? 1000 : $expireTime;
        $dateExpired = DateTime::getTimestamp() + $expireTime;
        $token = ($type == Self::LOGIN_ACCESS_TOKEN) ? Utils::createRandomKey() : UuidFactory::create();

        if ($deleteExpired == true) {          
            $result = $this->deleteExpired($userId,$type);
        }
        
        $model = $this->getTokenByUser($userId,$type);
        if (is_object($model) == true) {
            return $model;
        }

        $info = [
            'user_id'      => $userId,
            'token'        => $token,
            'date_expired' => $dateExpired,
            'type'         => $type
        ];
        $model = $this->create($info);

        return (is_object($model) == true) ? $model : false;
    }

    /**
     * Remove access token
     *
     * @param string $token
     * @return boolean
     */
    public function removeToken($token)
    {
        $model = $this->findByColumn($token,['uuid','token']);
        if (is_object($model) == true) {
            return $model->delete();
        }
        return true;
    }

    /**
     * Get access token
     *
     * @param  string $token
     * @return string|null
     */
    public function getToken($token)
    {      
        $model = $this->findByColumn($token,'token');
        return (is_object($model) == true) ? $model : null;
    }

    /**
     * Return true if token is expired
     *
     * @param string $token
     * @return boolean
     */
    public function isExpired($token)
    {
        $model = $this->findByColumn($token,'token');
        if (is_object($model) == false) {
            return true;
        }
        if ($model->date_expired == -1) {
            return false;
        }

        return (DateTime::getTimestamp() > $model->date_expired || empty($model->date_expired) == true) ? true : false;
    }

    /**
     * Find token
     *
     * @param integer $userId
     * @param integer $type
     * @return mxied
     */
    public function getTokenByUser($userId, $type = AccessTokens::PAGE_ACCESS_TOKEN)
    {
        $model = $this->where('user_id','=',$userId)->where('type','=',$type)->first();

        return (is_object($model) == true) ? $model : false;
    }

    /**
     * Return true if token exist
     *
     * @param integer $userId
     * @param integer $type
     * @return boolean
     */
    public function hasToken($userId, $type = AccessTokens::PAGE_ACCESS_TOKEN)
    {    
        return is_object($this->getTokenByUser($userId,$type));
    }

    /**
     * Delete expired token
     *
     * @param integer $userId
     * @param integer|null $type
     * @return void
     */
    public function deleteExpired($userId, $type = AccessTokens::PAGE_ACCESS_TOKEN)
    {
        $model = $this->where('date_expired','<',DateTime::getTimestamp())
            ->where('date_expired','<>',-1)
            ->where('user_id','=', $userId);
        
        if ($type != null) {
            $model = $model->where('type','=',$type);
        }

        return $model->delete();
    }

    /**
     * Delete all expired tokens
     *
     * @return bool
     */
    public function deleteExpiredTokens()
    {
        return $this->where('date_expired','<',DateTime::getTimestamp())->where('date_expired','<>',-1)->delete();
    }

    /**
     * Get all tokens for user
     *
     * @param integer $userId
     * @return null|Model
     */
    public function getUserTokens($userId)
    {
        return $this->where('user_id','=',$userId)->get();
    }
}
