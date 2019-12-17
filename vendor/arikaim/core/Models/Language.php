<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Position;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\DefaultTrait;

/**
 * Language database model
 */
class Language extends Model  
{
    use Uuid,
        Find,
        Status,
        DefaultTrait,
        Position;
    
    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'language';
    
    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [
        'code',
        'title',
        'native_title',
        'code_3',
        'country_code'
    ];
   
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;
    
    /**
     * Mutator (set) for code attribute.
     *
     * @param string $value
     * @return void
     */
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtolower($value);
    }

    /**
     * Return true if language exist
     *
     * @param string $code
     * @param integer $status
     * @return boolean
     */
    public function has($code, $status = null)
    {
        $model = $this->where('code','=',$code);
        if ($status == true) {
            $model = $model->where('status','=',Self::$ACTIVE);
        }
        $model = $model->first();
        return (is_object($model) == true) ? true : false;           
    }

    /**
     * Add language record
     *
     * @param array $language
     * @return Model|bool
     */
    public function add(array $language)
    {
        return ($this->has($language['code']) == true) ? false : $this->create($language);           
    }

    /**
     * Return default language
     *
     * @return void
     */
    public function getDefaultLanguage()
    {
        try {
            $model = $this->getDefault();
            return (is_object($model) == true) ? $model->code : "en";                    
        } catch(\Exception $e) {
        }
        return 'en';
    }
}
