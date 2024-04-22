<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits;

/**
 * Update Status field
 * 
 * Default status column name in model:
 *      protected $statusColumn = 'column name';
*/
trait Status 
{        
    /**
     * Default status column name
     *
     * @var string
     */
    protected static $DEFAULT_STATUS_COLUMN = 'status';

    /**
     * Disabled
     */
    static $DISABLED = 0;

    /**
     * Active
     */
    static $ACTIVE = 1;
    
    /**
     * Completed
     */
    static $COMPLETED = 2;  

    /**
     * Published
     */
    static $PUBLISHED = 3;  

    /**
     * Pending activation
     */
    static $PENDING = 4;

    /**
     *  Suspended
     */
    static $SUSPENDED = 5;

    /**
     *  Cancelled
     */
    static $CANCELLED = 6;

    /**
     * Return active value
     *
     * @return integer
     */
    public function ACTIVE(): int
    {
        return Self::$ACTIVE;
    }

    /**
     * Return disabled value
     *
     * @return integer
     */
    public function DISABLED(): int
    {
        return Self::$DISABLED;
    }

    /**
     * Return completed value
     *
     * @return integer
     */
    public function COMPLETED(): int
    {
        return Self::$COMPLETED;
    }

    /**
     * Pending activation
     *
     * @return integer
     */
    public function PENDING(): int
    {
        return Self::$PENDING;
    }

    /**
     * Suspended
     *
     * @return integer
     */
    public function SUSPENDED(): int
    {
        return Self::$SUSPENDED;
    }

    /**
     * Cancelled
     *
     * @return integer
     */
    public function CANCELLED(): int
    {
        return Self::$CANCELLED;
    }

    /**
     * Status text
     *
     * @var array
     */
    protected $statusText = [
        'disabled',
        'active',
        'completed',
        'published',
        'pending',
        'suspended',
        'cancelled'
    ];

    /**
     * Status scope
     *
     * @param Builder $query
     * @param mixed $items
     * @return Builder
     */
    public function scopeStatusQuery($query, $items)
    {
        return (\is_array($items) == true) ? 
            $query->whereIn($this->statusColumnName ?? static::$DEFAULT_STATUS_COLUMN,$items) : 
            $query->where($this->statusColumnName ?? static::$DEFAULT_STATUS_COLUMN,'=',$items);
    }

    /**
     * Resolve status id
     *
     * @param string|int $status
     * @return integer|false
     */
    public function resolveStatusText($status) 
    {
        return (\is_numeric($status) == true) ? $status : \array_search($status,$this->statusText);
    } 

    /**
     * Return active model query builder
     *
     * @return Builder
     */
    public function getActive()
    {
        $query = $this->activeQuery();
        
        return (\method_exists($query,'getNotDeletedQuery') == true) ? $query->getNotDeletedQuery() : $query;        
    }
    
    /**
     * Active status scope
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActiveQuery($query)
    {       
        return $query->where($this->statusColumnName ?? static::$DEFAULT_STATUS_COLUMN,'=',Self::$ACTIVE);
    }

    /**
     * Return disabled model query builder
     *
     * @return Builder
     */
    public function getDisabled()
    {
        return $this->where($this->statusColumnName ?? static::$DEFAULT_STATUS_COLUMN,'=',Self::$DISABLED);
    }

    /**
     * Set model status
     *
     * @param integer|string|null $status
     * @return bool
     */
    public function setStatus($status = null): bool
    {     
        $this->{$this->statusColumnName ?? static::$DEFAULT_STATUS_COLUMN} = $this->resolveStatusValue($status);

        return (bool)$this->save();         
    }

    /**
     * Get status value
     *
     * @param integer|null|string $status
     * @return integer
     */
    public function resolveStatusValue($status = null): int
    {     
        if ($status === 'toggle') {     
            return ($this->{$this->statusColumnName ?? static::$DEFAULT_STATUS_COLUMN} == 1) ? 0 : 1;
        }

        return (empty($status) == true) ? 0 : (int)$status;
    }
}
