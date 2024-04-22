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
use Arikaim\Core\Db\Traits\DateCreated;

/**
 * ImageCollectionItems class
 */
class ImageCollectionItems extends Model  
{
    use 
        Uuid,
        DateCreated,
        Find;
       
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'image_collection_items';

    /** 
     *  Inclide relations
     */
    protected $with = [
        'image'
    ];
    
    /**
     * Fillable columns
     *
     * @var array
     */
    protected $fillable = [
        'image_id',
        'collection_id',
        'date_created'    
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Image relation
     *
     * @return Relation|null
     */
    public function image()
    {
        return $this->belongsTo(Image::class,'image_id');
    }
}
