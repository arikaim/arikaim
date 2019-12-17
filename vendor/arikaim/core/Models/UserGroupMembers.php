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
use Arikaim\Core\Db\Traits\DateCreated;

/**
 * User groups details database model
 */
class UserGroupMembers extends Model  
{
    use Uuid,
        DateCreated;

    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [        
        'user_id',
        'groupd_id',
        'date_expire'
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
     * @return void
     */
    public function group()
    {
        return $this->hasOne(UserGroups::class,'groupd_id','id');     
    }

    /**
     * User relation
     *
     * @return void
     */
    public function user()
    {
        return $this->hasOne(Users::class,'user_id','id');     
    }
}
