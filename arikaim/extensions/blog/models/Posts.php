<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Blog\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Slug;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\UserRelation;
use Arikaim\Core\Db\Traits\DateCreated;
use Arikaim\Core\Db\Traits\DateUpdated;
use Arikaim\Core\Db\Traits\SoftDelete;

use Arikaim\Extensions\Category\Models\Traits\CategoryRelations;
use Arikaim\Extensions\Image\Models\Traits\ImageRelation;

/**
 * Posts model class
 */
class Posts extends Model
{
    use Uuid,     
        Find,   
        Slug,   
        Status,
        DateCreated,
        DateUpdated,
        SoftDelete,
        CategoryRelations,  
        ImageRelation,   
        UserRelation;
    
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'position',       
        'status',
        'slug',
        'title',      
        'content',
        'summary',
        'content_type',
        'image_id',
        'date_created',
        'date_updated',
        'date_deleted',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'user_id'
    ];
    
    /**
     * Visible columns
     *
     * @var array
     */
    protected $visible = [
        'uuid',           
        'date_created',      
        'slug',
        'title',  
        'date_updated',
        'key',        
        'categories',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'summary',
        'content_type',
        'content'              
    ];

    /**
     * Include relations
     *
     * @var array
     */
    protected $with = [              
        'categories'
    ];

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false; 

    /**
     * Content provider name
     *
     * @var string
     */
    protected $contentProviderName = 'blog.post';

    /**
     * Content provider title
     *
     * @var string
     */
    protected $contentProviderTitle = 'Blog Posts';

    /**
     * Content provider category
     *
     * @var string|null
     */
    protected $contentProviderCategory = null;

    /**
     * Supported content types
     *
     * @var array
     */
    protected $supportedContentTypes = ['blog.post'];

    /**
     * Delete post
     *
     * @return boolean
     */
    public function deletePost(): bool
    {
        return ($this->delete() !== false);
    }

    /**
     * Find posts
     *
     * @param Builder      $query
     * @param string       $key
     * @param integer|null $userId
     * @return Builder
     */
    public function scopeFindPostQuery($query, string $key, ?int $userId)
    {
        if (empty($userId) == false) {
            $query = $query->where('user_id','=',$userId);
        }
    
        return $query->where(function($query) use($key) {
            $query
                ->where('title','=',$key)
                ->orWhere('slug','=',$key);
        });
    }

    /**
     * Return true if post exist
     *
     * @param string $title or slug
     * @param int|null $userId
     * @return boolean
     */
    public function hasPost(string $key, ?int $userId = null): bool
    {
        return ($this->findPost($key,$userId) != null);
    }

    /**
     * Get published posts
     *
     * @param integer $pageId
     * @return Builder
     */
    public function scopePublishedQuery($query)
    {
        return $query->where('status','=',1);
    }

    /**
     * Find post
     *
     * @param string $key
     * @param int|null $userId
     * @return Model|null
     */
    public function findPost(string $key, ?int $userId): ?object
    {
        return $this->findPostQuery($key,$userId)->first();      
    }
}
