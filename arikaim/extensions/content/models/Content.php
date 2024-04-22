<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Content\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\UserRelation;
use Arikaim\Core\Db\Traits\Status;

/**
 * Content db model class
 */
class Content extends Model 
{
    use Uuid, 
        Status,    
        Find,       
        UserRelation;
    
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'content';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'status', 
        'key',
        'title',
        'content_type',
        'content_id',
        'user_id'
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;
    
    /**
     * Save item
     *
     * @param string       $key
     * @param string       $contentType
     * @param string|int   $contentId
     * @param string|null  $title
     * @param integer|null $userId
     * @return mixed
     */
    public function saveItem(string $key, string $contentType, $contentId, ?string $title, ?int $userId = null)
    {
        if ($this->hasContentItem($key,$userId) == true) {
            return $this->update([
                'content_type' => $contentType
            ]);
        }

        return $this->create([
            'key'           => $key,
            'content_type'  => $contentType,
            'content_id'    => $contentId,
            'title'         => $title,
            'user_id'       => $userId
        ]);
    }

    /**
     * Find content by key
     *
     * @param string  $key
     * @param integer|null $userId
     * @return object|null
     */
    public function findByKey(string $key, ?int $userId): ?object
    {
        return $this->findByKeyQuery($key,$userId)->first();
    }

    /**
     * Return true if content item exist
     *
     * @param string  $key
     * @param integer|int $userId
     * @return boolean
     */
    public function hasContentItem(string $key, ?int $userId): bool
    {
        return ($this->findByKey($key,$userId) !== null);
    }

    /**
     * Find by key query
     *
     * @param Builder  $query
     * @param string  $key
     * @param integer|null $userId
     * @return Builder
     */
    public function scopeFindByKeyQuery($query, string $key, ?int $userId)
    {
        return $query
            ->userQuery($userId)
            ->where('key','=',\trim($key));
    }
}
