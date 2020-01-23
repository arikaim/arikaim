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
use Arikaim\Core\Db\Traits\PolymorphicRelations;

/**
 * CategoryRelations class
 */
class CategoryRelations extends Model  
{
    use 
        Uuid,
        PolymorphicRelations,
        Find;
       
    /**
     * Table name
     *
     * @var string
     */
    protected $table = "category_relations";

    /**
     * Fillable columns
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'relation_id',
        'relation_type'       
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Relation model class
     *
     * @var string
     */
    protected $relationModelClass = Category::class;

    /**
     * Reation column name
     *
     * @var string
     */
    protected $relationColumnName = 'category_id';
}
