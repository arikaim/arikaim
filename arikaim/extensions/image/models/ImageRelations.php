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

use Arikaim\Extensions\Image\Models\Image;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\PolymorphicRelations;

/**
 * ImageRelations class
 */
class ImageRelations extends Model  
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
    protected $table = 'image_relations';

    /**
     * Fillable columns
     *
     * @var array
     */
    protected $fillable = [
        'image_id',
        'relation_id',
        'relation_type'       
    ];
    
    /**
     * With relations
     *
     * @var array
     */
    protected $with = [                    
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
    protected $relationModelClass = Image::class;

    /**
     * Reation column name
     *
     * @var string
     */
    protected $relationColumnName = 'image_id';

    /**
     * Image relations
     *
     * @return Relation|null
     */
    public function image()
    {
        return $this->belongsTo(Image::class,'image_id');
    }
}
