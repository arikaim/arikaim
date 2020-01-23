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

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\ToggleValue;
use Arikaim\Core\Db\Traits\Position;
use Arikaim\Core\Db\Traits\Tree;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\UserRelation;
use Arikaim\Core\Db\Traits\Translations;

/**
 * Category class
 */
class Category extends Model  
{
    use Uuid,
        ToggleValue,        
        Position,
        Find,
        Status,
        UserRelation,
        Translations,
        Tree;
    
    /**
     * Table name
     *
     * @var string
     */
    protected $table = "category";

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
     * Append attributes to serialization
     *
     * @var array
     */
    protected $appends = [
        'title'
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
        'position',       
        'status',
        'parent_id',
        'branch',
        'user',
        'uuid',       
        'title'
    ];

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'position',       
        'status',
        'parent_id',
        'branch',
        'user_id'
    ];
   
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;
    
    /**
     * Parent category relation
     *
     * @return Model|null
     */
    public function parent()
    {
        return $this->belongsTo(Category::class,'parent_id');
    }
    
    /**
     * Set child category status
     *
     * @param integer $id
     * @param integer $status
     * @return void
     */
    public function setChildStatus($id, $status)
    {
        $model = $this->findById($id);
        if ($model == false) {
            return false;
        }
        $model = $model->where('parent_id','=',$model->id)->get();
        if (is_object($model) == false) {
            return false;
        }

        foreach ($model as $item) {   
            $item->setStatus($status);        
            $this->setChildStatus($item->id,$status);
        }   
    }

    /**
     * Delete category 
     *
     * @param integer $id
     * @param boolean $removeChild
     * @return void
     */
    public function remove($id, $removeChild = true)
    {
        if ($removeChild == true) {
            $this->removeChild($id);
        }
        $model = $this->findById($id);
        if (is_object($model) == false) {
            return false;
        }
        $relations = DbModel::CategoryRelations('category');
        $relations->deleteRelations($model->id);
        $model->removeTranslations();

        return $model->delete();      
    }

    /**
     * Remove child category
     *
     * @param integer $id
     * @return boolean
     */
    public function removeChild($id)
    {
        $model = $this->findById($id);
        if ($model == false) {
            return false;
        }
        $model = $model->where('parent_id','=',$model->id)->get();
        if (is_object($model) == false) {
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
     * @param string|null $language
     * @param array $items
     * @return array|null
     */
    public function getTitle($id = null, $language = null, $items = [])
    {       
        $model = (empty($id) == true) ? $this : $this->findById($id);

        if (is_object($model) == false) {
            return null;
        }

        $result = $items;
        if (empty($model->parent_id) == false) {
           $result = $model->getTitle($model->parent_id,$language,$result);        
        }     
        $title = $model->getTranslationTitle($language);
        $title = (empty($title) == true) ? $model->getTranslationTitle('en') : $title;
        $result[] = $title;

        return $result;
    }

    /**
     *  Get categories list
     *
     * @param integer $parentId
     * @param string|null $branch
     * @return Model|null
     */
    public function getList($parentId = null, $branch = null)
    {   
        $model = (empty($branch) == false) ? $this->where('branch','=',$branch) : $this;       
        $model = $model->where('parent_id','=',$parentId)->get();

        return (is_object($model) == true) ? $model : null;           
    }

    /**
     * Get translation title
     *
     * @param string|null $language
     * @param string|null $default
     * @return string|null
     */
    public function getTranslationTitle($language = null, $default = null)
    {
        $model = $this->translation($language);     
        if ($model == false) {
            return $default; 
        } 
        
        return (isset($model->title) == true) ? $model->title : null;
    }

    /**
     * Title attribute
     *
     * @return string|null
     */
    public function getTitleAttribute()
    {
        return $this->getTranslationTitle();        
    }

    /**
     * Return true if category exist
     *
     * @param string $title
     * @param integer|null $parentId
     * @param string|null $branch
     * @return boolean
     */
    public function hasCategory($title, $parentId = null, $branch = null)
    { 
        return is_object($this->findCategory($title,$parentId,$branch));
    }

    /**
     * Find category
     *
     * @param string $title
     * @param integer|null $parentId
     * @param string|null $branch
     * @return Model|false
     */
    public function findCategory($title, $parentId = null, $branch = null)
    {
        $model = (empty($branch) == false) ? $this->where('branch','=',$branch) : $this;     
        $model = $model->where('parent_id','=',$parentId)->get();

        foreach ($model as $item) {
            $translation = $item->translations()->getQuery()->where('title','=',$title)->first();   
            if (is_object($translation) == true) {
                return $item;
            }  
        }
        
        return false;
    }

    /**
     * Create categories from array
     *
     * @param array $items
     * @param integer|null $parentId
     * @param string|null $language
     * @param string|null $branch
     * @return array
     */
    public function createFromArray(array $items, $parentId = null, $language = null, $branch = null)
    {
        $result = [];
        foreach ($items as $key => $value) {       
            $model = $this->findTranslation('title',$value);
            if (is_object($model) == false) {                                  
                $model = $this->create([
                    'parent_id' => $parentId,
                    'branch'    => $branch
                ]);
                $model->saveTranslation(['title' => $value], $language, null); 
            }
            $result[] = $model->id;            
        }      

        return $result;
    }

    /**
     * Build category relations query
     *
     * @param Model $filterModel
     * @param string $categorySlug
     * @return Model
     */
    public function relationsQuery($filterModel, $categorySlug)
    {
        if (empty($categorySlug) == false) {
            $categoryTranslations = DbModel::create($this->translationModelClass,'category',function($model) use($categorySlug) {                
                return $model->findBySlug($categorySlug);           
            });

            $filterModel = $filterModel->whereHas('categories',function($query) use($categoryTranslations) {
                $query->where('category_id','=',$categoryTranslations->id);
            });

            return $filterModel;
        }

        return $filterModel;
    }
}
