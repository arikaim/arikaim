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

use Arikaim\Core\Packages\Interfaces\PackageRegistryInterface;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Position;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\PackageRegistry;

/**
 * Extensions database model
 */
class Extensions extends Model implements PackageRegistryInterface
{
    use Uuid,
        Find,
        Position,
        PackageRegistry,
        Status;

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'title',
        'description',
        'short_description',
        'class',
        'type',
        'position',
        'version',       
        'admin_menu',
        'console_commands',
        'license_key'
    ];
    
    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'extensions';

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;
    
    /**
     * Mutator (set) for admin_menu attribute.
     *
     * @param array|null $value
     * @return void
     */
    public function setAdminMenuAttribute($value)
    {
        $value = (is_array($value) == true) ? $value : [];         
        $this->attributes['admin_menu'] = json_encode($value);
    }

    /**
     * Mutator (set) for console_commands attribute.
     *
     * @param array:null $value
     * @return void
     */
    public function setConsoleCommandsAttribute($value)
    {
        $value = (is_array($value) == true) ? $value : [];    
        $this->attributes['console_commands'] = json_encode($value);
    }

    /**
     * Mutator (get) for console_commands attribute.
     *
     * @return array
     */
    public function getConsoleCommandsAttribute()
    {
        return json_decode($this->attributes['console_commands'],true);
    }

    /**
     * Mutator (get) for admin_menu attribute.
     *
     * @return string|null
     */
    public function getAdminMenuAttribute()
    {
        return json_decode($this->attributes['admin_menu'],true);
    }
}
