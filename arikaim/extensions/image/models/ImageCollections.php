<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Image\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Extensions\Image\Models\ImageCollectionItems;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Slug;
use Arikaim\Core\Db\Traits\DateCreated;
use Arikaim\Core\Db\Traits\UserRelation;

/**
 * ImageCollections class
 */
class ImageCollections extends Model  
{
    use 
        Uuid,
        Slug,
        DateCreated,
        UserRelation,
        Find;
       
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'image_collections';

    /**
     * Fillable columns
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'slug',
        'description',
        'user_id',
        'date_created',
        'title'       
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Retrun true if image exsit in collection
     *
     * @param integer $imageId
     * @return boolean
     */
    public function hasImage(int $imageId): bool
    { 
        $item = $this->whereHas('items', function($query) use($imageId) {
            return $query->where('image_id','=',$imageId);
        })->first();
  
        return ($item == null) ? false : ($item->id == $this->id);
    }

    /**
     * Add image to collection
     *
     * @param integer    $imageId
     * @return boolean
     */
    public function addImage(int $imageId): bool
    {
        if ($this->hasImage($imageId) == true) {
            return true;
        }

        $result = $this->items()->create([
            'image_id' => $imageId
        ]);

        return ($result !== false);
    }

    /**
     * Collection items relation
     *
     * @return Relation|null
     */
    public function items()
    {
        return $this->hasMany(ImageCollectionItems::class,'collection_id');
    }

    /**
     * Find collection scope query
     *
     * @param Builder      $query
     * @param string       $slug
     * @param integer $userId
     * @return Builder
     */
    public function scopeFindCollectionQuery($query, string $slug, int $userId)
    {
        return $query
            ->where('slug','=',$slug)
            ->where('user_id','=',$userId);
    }

    /**
     * Find collection
     *
     * @param string       $slug
     * @param integer $userId
     * @return object|null
     */
    public function findCollection(string $slug, int $userId = null): ?object
    {
        $model = $this->findCollectionQuery($slug,$userId)->first();

        return ($model != null) ? $model : $this->findById($slug);
    }

    /**
     * Save collection
     *
     * @param string       $title
     * @param string|null  $slug
     * @param integer|null $userId
     * @return Model|false
     */
    public function saveCollection(string $title, ?string $slug = null, ?int $userId = null)
    {
        $slug = (empty($slug) == true) ? $this->createSlug($title) : $slug;
        $model = $this->findCollection($slug,$userId);
        $data = [
            'title'   => $title,
            'slug'    => $slug,
            'user_id' => $userId
        ];

        if ($model == null) {
            $created = $this->create($data);
            return ($created != null) ? $created : false;
        }
        
        return ($model->update($data) !== false) ? $model : false;
    }

    /**
     * Delete collection
     *
     * @return boolean
     */
    public function deleteCollection(): bool
    {
        // delete items
        $this->items()->delete();
        // delete
        return ($this->delete() !== false);
    }

}
