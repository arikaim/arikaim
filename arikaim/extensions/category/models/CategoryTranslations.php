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

class CategoryTranslations extends Model  
{
    use 
        Uuid,
        Slug,
        Find;
       
    protected $table = "category_translations";

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'language'
    ];
   
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
}
