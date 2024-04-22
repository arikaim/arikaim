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

use Arikaim\Extensions\Category\Models\Category;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Slug;

/**
 * Category translations
 */
class CategoryTranslations extends Model  
{
    use 
        Uuid,
        Slug,   
        Find;
       
    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'category_translations';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'language'
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Category relation
     *
     * @return mixed
     */
    public function category()
    {
        return $this->hasOne(Category::class,'id','category_id'); 
    }

    /**
     * Find Category
     *
     * @param string $slug
     * @param string $language
     * @return Model|null
     */
    public function findCategory(string $slug, string $language): ?object
    {
        return $this->where('slug','=',$slug)->where('language','=',$language)->first();
    }

    /**
     * Find Category Id
     *
     * @param string $slug
     * @param string $language
     * @return int|null
     */
    public function findCategoryId(string $slug, string $language = 'en'): ?int
    {
        $model = $this->findCategory($slug,$language);
        return (\is_object($model) == false) ? null : $model->id;
    }

    /**
     * Find translated slug
     *
     * @param string $engSlug
     * @param string $language
     * @return string
     */
    public function findTranslatedSlug(string $engSlug, string $language): string
    {
        $enTranslation = $this->where('slug','=',$engSlug)->where('language','=','en')->first();
        if (\is_object($enTranslation) == false) {
            return $engSlug;
        }

        $model = $this->where('language','=',$language)->where('category_id','=',$enTranslation->category_id)->first();

        return (\is_object($model) == true) ? $model->slug : $engSlug;
    }
}
