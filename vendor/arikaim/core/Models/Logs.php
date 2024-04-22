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
use Arikaim\Core\Db\Traits\Find;

/**
 * Db handler for monolog
 */
class Logs extends Model
{
    use        
        Uuid,
        Find;

    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [
        'message',
        'level_name',
        'channel',
        'context',
        'date_created',
        'extra',
        'level'
    ];
    
    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'logs';

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Mutator (get) for context attribute.
     *
     * @return array
     */
    public function getContextAttribute()
    {
        return (empty($this->attributes['context']) == true) ? [] : \json_decode($this->attributes['context'],true);
    }

    /**
     * Mutator (get) for extra attribute.
     *
     * @return array
     */
    public function getExtraAttribute()
    {
        return (empty($this->attributes['extra']) == true) ? [] : \json_decode($this->attributes['extra'],true);
    }

    /**
     * Logs level query
     *
     * @param Builder $query
     * @param string $level
     * @return Builder
     */
    public function scopeLogsLevelQuery($query, $level)
    {
        return $query->where('level','=',$level);
    }
}
