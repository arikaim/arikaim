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
*/
trait UserRelation 
{    
    /**
     * Init model events.
     *
     * @return void
     */
    public static function bootUserRelation()
    {
        static::creating(function($model) {
            $userId = $model->getUserIdAttributeName();   
            if (empty($model->attributes[$userId]) == true) {  
                $authId = $model->getAuthId();               
                $model->attributes[$userId] = (empty($authId) == true) ? null : $authId;
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
        return (isset($this->authId) == true) ? $this->authId : null;
    }

    /**
     * Get user id attribute name
     *
     * @return string
     */
    public function getUserIdAttributeName()
    {
        return (isset($this->userIdColumn) == true) ? $this->userIdColumn : 'user_id';
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function user()
    {      
        return $this->belongsTo(Users::class,$this->getUserIdAttributeName());
    }
}
