<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits;

use Arikaim\Core\Models\Users;

/**
 * User Relation trait
 *      
 * Define custom user column
 * 
 *  protected $userColumnName = 'column name';
*/
trait UserRelation 
{    
    /**
     * Default user column name
     *
     * @var string
     */
    protected static $DEFAULT_USER_COLUMN = 'user_id';

    /**
     * Init model events.
     *
     * @return void
     */
    public static function bootUserRelation()
    {
        static::creating(function($model) {
            $columnName = $model->userColumnName ?? static::$DEFAULT_USER_COLUMN;

            if (empty($model->attributes[$columnName]) == true) {  
                $authId = $model->getAuthId();               
                $model->attributes[$columnName] = (empty($authId) == true) ? null : $authId;
            }
        });
    }

    /**
     * Get current auth id
     *
     * @return mixed
     */
    public function getAuthId()
    {
        return $this->authId ?? null;
    }

    /**
     * Get user relation
     *
     * @return Relation|null
     */
    public function user()
    {      
        return $this->belongsTo(Users::class,$this->userColumnName ?? static::$DEFAULT_USER_COLUMN);
    }

    /**
     * Filter by user
     *
     * @param Builder $query
     * @param integer|null $userId
     * @return Builder
     */
    public function scopeUserQuery($query, ?int $userId)
    {
        if ($userId === null) {
            return $query->whereNull($this->userColumnName ?? static::$DEFAULT_USER_COLUMN);
        }

        return (empty($userId) == false) ? $query->where($this->userColumnName ?? static::$DEFAULT_USER_COLUMN,'=',$userId) : $query;         
    }

    /**
     * Filter rows by user + null (public)
     *
     * @param Builder      $query
     * @param integer|null $userId
     * @return Builder
     */
    public function scopeUserQueryWithPublic($query, ?int $userId)
    {
        return (empty($userId) == true) ? $query->whereNull($this->userColumnName ?? static::$DEFAULT_USER_COLUMN) :
            $query
                ->where($this->userColumnName ?? static::$DEFAULT_USER_COLUMN,'=',$userId)
                ->orWhereNull($this->userColumnName ?? static::$DEFAULT_USER_COLUMN);
    }
}
