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

use Arikaim\Core\Utils\File;
use Arikaim\Extensions\Image\Models\ImageThumbnails;
use Arikaim\Extensions\Image\Models\ImageRelations;
use Arikaim\Extensions\Image\Classes\ImageLibrary;

use Arikaim\Core\Utils\Path;
use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\UserRelation;
use Arikaim\Core\Db\Traits\DateCreated;
use Arikaim\Core\Db\Traits\FileTypeTrait;

/**
 * Image db model class
 */
class Image extends Model  
{
    use Uuid,     
        Find, 
        Status,
        DateCreated,  
        UserRelation, 
        FileTypeTrait;
    
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'image';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'private', 
        'file_size',
        'mime_type',
        'file_name', 
        'base_name',
        'category_id',
        'url',
        'width',
        'height',      
        'user_id',
        'deny_delete'       
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;
   
    /**
     * Image relation in all collections
     *
     * @return Relation|null
     */
    public function collectionItems()
    {
        return $this->hasMany('Arikaim\\Extensions\\Image\\Models\\ImageCollectionItems','image_id');
    }

    /**
     * Category relation
     *
     * @return Relation|null
     */
    public function category()
    {
        return $this->belongsTo('Arikaim\\Extensions\\Category\\Models\\Category','category_id');
    }

    /**
     * Create thumbnail model
     *   
     * @param integer $width
     * @param integer $height
     * @return Model|null
    */
    public function createThumbnail(int $width, int $height)
    {
        $thumbails = new ImageThumbnails(); 
        
        return $thumbails->createThumbnail($width,$height,$this->image_id);      
    }

    /**
     * src attribute
     *
     * @return string
     */
    public function getSrcAttribute()
    {
        if (empty($this->url) == false) {
            return $this->url;
        }
        
        if ($this->private == 1) {
            return '/api/image/view/' . $this->uuid;
        }

        return \str_replace("arikaim/storage/public",'public',$this->file_name); 
    }

    /**
     * Get image path
     *
     * @param boolean $relative
     * @return string
     */
    public function getImagePath(bool $relative = true): string
    {
        return ($relative == true) ? $this->file_name : Path::STORAGE_PATH . $this->file_name;
    }

    /**
     * Get user images query
     *
     * @param Builder $query
     * @param int|null $userId
     * @param int|null $categoryId
     * @return Builder
     */
    public function scopeUserImagesQuery($query, ?int $userId, ?int $categoryId = null)
    {
        $userId = (empty($userId) == true) ? $this->user_id : $userId;
        if (empty($categoryId) == false) {
            $query = $query->where('category_id','=',$categoryId);
        }
        if (empty($userId) == false) {
            $query = $query->where('user_id','=',$userId);
        }

        return $query;
    }

    /**
     * Thumbnails relation
     *
     * @return Relation|null
     */
    public function thumbnails()
    {
        return $this->hasMany(ImageThumbnails::class,'image_id');
    }

    /**
     * Image relations
     *
     * @return Relation|null
     */
    public function relations()
    {
        return $this->hasMany(ImageRelations::class,'image_id');
    }

    /**
     * Thumbnail
     *
     * @param integer $width
     * @param integer $height
     * @return Model|null
     */
    public function thumbnail(int $width, int $height): ?object
    {
        return $this->thumbnails->where('width','=',$width)->where('height','=',$height)->first();
    }

    /**
     * Get smallest thumbnail
     *
     * @return Model|null
     */
    public function thumbnailSmall(): ?object
    {
        return $this->thumbnails()->orderBy('width','asc')->first();
    }

    /**
     * Get large thumbnail
     *
     * @return Model|null
     */
    public function thumbnailLarge(): ?object
    {
        return $this->thumbnails()->orderBy('width','desc')->first();
    }

    /**
     * Find image
     *
     * @param string $name
     * @param string|null $excludeId
     * @return Model|null
     */
    public function findImage(string $name, ?string $excludeId = null): ?object
    {
        // by id, uuid
        $query = $this->where(function($query) use ($name,$excludeId) {
            $query->where('uuid','=',$name);
            if (empty($excludeId) == false) {
                $query->where('uuid','<>', $excludeId);
            }
        })->orWhere(function($query) use ($name,$excludeId) {
            $query->where('file_name','=',$name);
            if (empty($excludeId) == false) {
                $query->where('uuid','<>', $excludeId);
            }
        })->orWhere(function($query) use ($name,$excludeId) {
            $query->where('url','=',$name);
            if (empty($excludeId) == false) {
                $query->where('uuid','<>', $excludeId);
            }
        })->orWhere(function($query) use ($name,$excludeId) {
            $query->where('base_name','=',$name);
            if (empty($excludeId) == false) {
                $query->where('uuid','<>', $excludeId);
            }
        });
        
        return $query->first(); 
    } 

    /**
     * Check if image file exists
     *
     * @param string $name
     * @param int|int $userid
     * @param string|null $excludeId
     * @return boolean
     */
    public function hasImage(string $name, ?string $excludeId = null): bool
    { 
        return ($this->findImage($name,$excludeId) != null);
    }

    /**
     * Delete image and relations 
     *
     * @param string|null $name     
     * @return boolean
     */
    public function deleteImage(?string $name = null): bool
    {
        $model = (empty($name) == true) ? $this : $this->findImage($name);
        if ($model == null) {
            return false;
        }
        // delete thumbnails
        foreach ($this->thumbnails()->get() as $item) {
            $item->deleteThumbnail();
        };

        // delete thumbnail path
        $thumbnailPath = ImageLibrary::getThumbnailsPath($model->id,false);
        File::deleteDirectory($thumbnailPath,false);
       
        // delete relations
        $this->relations()->delete();

        // delete image file
        $model->deleteImageFile();
     
        // delete from collections
        $this->collectionItems()->delete();

        return (bool)$model->delete();        
    } 

    /**
     * Delete image file 
     *
     * @param string $fileName   
     * @return boolean
     */
    public function deleteImageFile(?string $fileName = null): bool
    {
        $fileName = $fileName ?? $this->file_name;
        $path = ROOT_PATH . BASE_PATH . $fileName; 

        return (File::exists($path) == true) ? File::delete($path) : true;         
    }     
}
