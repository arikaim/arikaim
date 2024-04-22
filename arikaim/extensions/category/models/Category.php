<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Category\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Db\Model as DbModel;
use Arikaim\Extensions\Category\Models\CategoryTranslations;
use Arikaim\Extensions\Category\Models\CategoryRelations;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Collection\Arrays;

use Arikaim\Core\Db\Traits\Slug;
use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\ToggleValue;
use Arikaim\Core\Db\Traits\Position;
use Arikaim\Core\Db\Traits\Tree;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\UserRelation;
use Arikaim\Core\Db\Traits\Translations;
use Arikaim\Core\Db\Traits\MetaTags;
use Arikaim\Extensions\Image\Models\Traits\ImageRelation;

/**
 * Category class
 */
class Category extends Model  
{
    use Uuid,
        ToggleValue,        
        Position,
        Find,
        Slug,
        Status,
        UserRelation,
        Translations,
        ImageRelation,
        MetaTags,
        Tree;
    
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'category';

    /**
     * Translation column ref
     *
     * @var string
     */
    protected $translationReference = 'category_id';

    /**
     * Translatin model class
     *
     * @var string
     */
    protected $translationModelClass = CategoryTranslations::class;

    /**
     * Translated attributes
     *
     * @var array
     */
    protected $translatedAttributes = [ 
        'title',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords'                 
    ];

    /**
     * With relations
     *
     * @var array
     */
    protected $with = [
        'translations',
        'user'
    ];
    
    /**
     * Visible columns
     *
     * @var array
     */
    protected $visible = [
        'id',
        'position',       
        'status',
        'parent_id',
        'branch',
        'user',
        'uuid'                     
    ];

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'position',  
        'title',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords', 
        'slug',     
        'status',
        'parent_id',
        'branch',
        'user_id',
        'image_id'
    ];
   
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;
    
    /**
     * Get category relations
     *
     * @return Relation|null
     */
    public function relations()
    {
        return $this->hasMany(CategoryRelations::class,'category_id')->without('category');
    }

    /**
     * Get categories id's
     *
     * @param array       $items
     * @param integer|null $parentId
     * @param string|null  $branch
     * @return array
     */
    public function getCategoryIds(array $items, ?int $parentId, ?string $branch = null): array
    {
        $result = [];
        foreach($items as $title) {
            $model = $this->findCategory($title,$parentId,$branch);
            if ($model !== null) {
                $result[] = $model->id;
            }
        }

        return $result;
    }

    /**
     * Parent category relation
     *
     * @return Relation|null
     */
    public function parent()
    {
        return $this->belongsTo(Category::class,'parent_id');
    }
    
    /**
     * Set child category status
     *
     * @param integer|string $id
     * @param integer $status
     * @return bool
     */
    public function setChildStatus($id, $status): bool
    {
        $model = $this->findById($id);
        if ($model == false) {
            return false;
        }
        $items = $model->where('parent_id','=',$model->id)->get();
        if (\is_object($items) == false) {
            return false;
        }

        foreach ($items as $item) {   
            $item->setStatus($status);        
            $this->setChildStatus($item->id,$status);
        }   

        return true;
    }

    /**
     * Delete category 
     *
     * @param integer|string $id
     * @param boolean $removeChild
     * @return bool
     */
    public function remove($id, bool $removeChild = true): bool
    {
        if ($removeChild == true) {
            $this->removeChild($id);
        }
        $model = $this->findById($id);
        if ($model == null) {
            return false;
        }
        $relations = DbModel::CategoryRelations('category');
        $relations->deleteRelations($model->id);
        $model->removeTranslations();

        return (bool)$model->delete();      
    }

    /**
     * Remove child category
     *
     * @param integer|string $id
     * @return boolean
     */
    public function removeChild($id): bool
    {
        $model = $this->findById($id);
        if ($model == false) {
            return false;
        }
        $model = $model->where('parent_id','=',$model->id)->get();
        if (\is_object($model) == false) {
            return false;
        }
        foreach ($model as $item) {
            $item->remove($item->id);          
        }
      
        return true;
    }

    /**
     * Get full cateogry title
     *
     * @param integer|string $id  
     * @param array $items
     * @return array|null
     */
    public function getTitle($id = null, $items = []): ?array
    {       
        $model = (empty($id) == true) ? $this : $this->findById($id);
        if ($model == null) {
            return null;
        }

        $result = $items;
        if (empty($model->parent_id) == false) {
           $result = $model->getTitle($model->parent_id,$result);        
        }     
    
        $result[] = $model->title;

        return $result;
    }

    /**
     * Get category slug with childs
     *
     * @return string
     */
    public function getSlug(): string
    {
        $items = $this->getTitle();
        if ($items === null) {
            return null;
        }

        return Utils::slug(Arrays::toString($items,'-'));
    }

    /**
     *  Get categories list
     *    
     * 
     * @param integer|null $parentId
     * @param string|null $branch
     * @return Model|null
     */
    public function getList(?int $parentId = null, ?string $branch = null)
    {   
        $model = (empty($branch) == false) ? $this->where('branch','=',$branch) : $this;       
        return $model->where('parent_id','=',$parentId)->get();
    }

    /**
     * Return true if category exist
     *
     * @param string $title
     * @param integer|null $parentId
     * @param string|null $branch
     * @return boolean
     */
    public function hasCategory(?string $title, ?int $parentId = null, ?string $branch = null): bool
    { 
        return ($this->findCategory($title,$parentId,$branch) != null);
    }
    
    /**
     * Find category
     *
     * @param string $title
     * @param integer|null $parentId
     * @param string|null $branch
     * @return Model|null
     */
    public function findCategory(?string $title, ?int $parentId = null, ?string $branch = null): ?object
    {
        $model = (empty($branch) == false) ? $this->where('branch','=',$branch) : $this;     
        $model = (empty($parentId) == true) ? $model->whereNull('parent_id') : $model->where('parent_id','=',$parentId);
        $model = $model
            ->where('title','=',$title)
            ->orWhere('slug','=',$title)
            ->first();
 
        return $model;
    }

    /**
     * Create categories from array
     *
     * @param array $items
     * @param integer|null $parentId   
     * @param string|null $branch
     * @return array
     */
    public function createFromArray(array $items, ?int $parentId = null, ?string $branch = null): array
    {
        $branch = $branch ?? null;
        $result = [];

        foreach ($items as $item) {         
            // set title
            $title = \trim($item['title'] ?? '');
            $title = (empty($title) == true) ? $item : $title;
            // set parent
            $parent = $item['parent_id'] ?? null;
            $parent = (empty($parent) == true) ? $parentId : $parent;

            if (empty($title) == true) continue;

            if ($this->hasCategory($title,$parent,$branch) == false) {      
                $model = $this->create([
                    'parent_id' => $parentId,
                    'title'     => $title,
                    'branch'    => $branch
                ]);

                if ($model !== null) {
                    $result[] = $model->id;   
                }
            } 
        }      

        return $result;
    }
}
