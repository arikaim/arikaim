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
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\PackageRegistry;

/**
 * Modules database model
 */
class Modules extends Model implements PackageRegistryInterface
{
    use Uuid,
        Find,
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
        'bootable',
        'status',
        'service_name',
        'version',     
        'console_commands',
        'facade_class',
        'facede_alias',
        'config',
        'category'
    ];
    
    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'modules';

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;
   
    /**
     * Mutator (set) for console_commands attribute.
     *
     * @param array|null $value
     * @return void
     */
    public function setConsoleCommandsAttribute($value)
    {
        $value = (\is_array($value) == true) ? $value : [];         
        $this->attributes['console_commands'] = \json_encode($value);
    }

    /**
     * Mutator (get) for console_commands attribute.
     *
     * @return array
     */
    public function getConsoleCommandsAttribute()
    {
        return (empty($this->attributes['console_commands']) == true) ? [] : \json_decode($this->attributes['console_commands'],true);
    }

    /**
     * Mutator (set) for config attribute.
     *
     * @param array $value
     * @return void
     */
    public function setConfigAttribute($value)
    {
        $value = (\is_array($value) == true) ? $value : [];    
        $this->attributes['config'] = \json_encode($value);
    }

    /**
     * Mutator (get) for config attribute.
     *
     * @return array
     */
    public function getConfigAttribute()
    {
        return (empty($this->attributes['config']) == true) ? [] : \json_decode($this->attributes['config'],true);
    }
}
