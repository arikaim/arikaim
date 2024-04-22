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
        'uuid',
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
     * Gte languages by codes
     *
     * @param array $codes
     * @return void
     */
    public function getLanguages(array $codes)
    {
        return $this->whereIn('code',$codes)->get();
    }

    /**
     * Find language by code
     *
     * @param string $code
     * @return object|null
     */
    public function findLanguage(string $code): ?object
    {
        $code = \strtolower(\trim($code));

        return $this->where('code','=',$code)->orWhere('code_3','=',$code)->first();
    }

    /**
     * Return true if language exist
     *
     * @param string $code
     * @param integer $status
     * @return boolean
     */
    public function has(string $code, ?int $status = null): bool
    {
        $model = $this->where('code','=',$code);
        if ($status == true) {
            $model = $model->where('status','=',Self::$ACTIVE);
        }
        $model = $model->first();

        return ($model != null);
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
     * @return string
     */
    public function getDefaultLanguage(): string
    {
        try {
            $model = $this->getDefault();
            return ($model != null) ? $model->code : 'en';                    
        } catch(\Exception $e) {
            return 'en';
        }
        
        return 'en';
    }
}
