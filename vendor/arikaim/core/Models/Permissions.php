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

use Arikaim\Core\Interfaces\Access\AccessInterface;
use Arikaim\Core\Utils\Utils;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Slug;

/**
 * Permissions database model
 */
class Permissions extends Model  
{
    use Uuid,
        Slug,
        Find;

    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [
        'name',
        'slug',
        'editable',
        'title',
        'description',
        'extension_name',
        'deny',
        'validator_class'
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
     * Custom slug source column
     *
     * @var string
     */
    protected $slugSourceColumn = 'name';

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
     * Mutator (get) for is_deny attribute.
     *
     * @return bool
     */
    public function getIsDenyAttribute()
    {
        return $this->isDeny();
    }

    /**
     * Return if permission is deny
     *
     * @return bool
     */
    public function isDeny(): bool
    {
        return (empty($this->deny) == true) ? false : (bool)$this->deny;
    }

    /**
     * Find permission model by id, uuid, slug, name
     *
     * @param mixed $value
     * @return Model|null
     */
    public function findPermission($value): ?object
    {
        return $this->findByColumn($value,['id','uuid','slug','name']);
    }

    /**
     * Return true if permission item exist.
     *
     * @param string $name
     * @return boolean
     */
    public function has(string $name): bool
    {
        return ($this->where('name','=',$name)->first() != null);      
    }

    /**
     * Create permission model
     *
     * @param string $name
     * @param string $title
     * @param string|null $description
     * @param int|null $deny
     * @return Model|false
     */
    public function createPermission(string $name, string $title = '', ?string $description = null, ?bool $deny = null)
    {
        $info = [
            'name'        => $name,
            'title'       => $title,
            'slug'        => Utils::slug($title),
            'description' => $description,
            'editable'    => true,
            'deny'        => (int)$deny ?? false
        ];

        $model = $this->findByColumn($name,'name');
        if ($model != null) {
            $model->update($info);
            return $model;
        }
       
        return $this->create($info);
    }

    /**
     * Get permission id 
     *
     * @param string $name  Name or Slug
     * @return integer|false
     */
    public function getId(string $name)
    {
        // find with slug
        $model = $this->where('slug','=',$name)->first();
        if ($model != null) {
            return $model->id;
        }
        // find by name
        $model = $this->where('name','=',$name)->first();

        return ($model != null) ? $model->id : false;    
    }

    /**
     * Get permisisons list query
     *
     * @return Builder
     */
    public function getListQuery()
    {
        return $this->where('name','<>',AccessInterface::CONTROL_PANEL)->orderBy('name');
    }
}
