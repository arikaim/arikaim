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

/**
 * Permissions database model
 */
class Permissions extends Model  
{
    use Uuid,
        Find;

    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [
        'name',
        'title',
        'description',
        'extension_name'
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
    protected $table = 'permissions';

    /**
     * Mutator (get) for title attribute.
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        return (empty($this->title) == true) ? $this->name : $this->title;
    }

    /**
     * Return true if permission item exist.
     *
     * @param string $name
     * @return boolean
     */
    public function has($name)
    {
        $model = $this->where('name','=',$name)->first();
        return (is_object($model) == false) ? false : true;            
    }

    /**
     * Get permission id 
     *
     * @param string $name
     * @return integer|false
     */
    public function getId($name)
    {
        $model = $this->where('name','=',$name)->first();
        return  (is_object($model) == true) ? $model->id : false;    
    }
}
