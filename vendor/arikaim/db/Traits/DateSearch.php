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

use Arikaim\Core\Utils\TimePeriod;

/**
 * Date search query
 * 
*/
trait DateSearch
{    
    /**
     * Column name for date period scopes
     *
     * @var string|null
     */
    protected $datePeriodColumnName;

    /**
     * Get year query scope
     *
     * @param Builder $query 
     * @param string $columnName
     * @param int|null $year
     * @return Builder
     */
    public function scopeYear($query, string $columnName, ?int $year = null)
    {     
        $period = TimePeriod::getYearPeriod($year);

        return $query->where($columnName,'>',$period['start'])->where($columnName,'<',$period['end']);
    }

    /**
     * Get month query scope
     *
     * @param Builder $query 
     * @param string $columnName
     * @param int|null $month
     * @param int|null $year
     * @return Builder
     */
    public function scopeMonth($query, string $columnName, ?int $month = null, ?int $year = null)
    {     
        $period = TimePeriod::getMonthPeriod($month,$year);
           
        return $query->where($columnName,'>',$period['start'])->where($columnName,'<',$period['end']);
    }

    /**
     * Get day query scope
     *
     * @param Builder $query 
     * @param string $columnName
     * @param int|null $day
     * @param int|null $month
     * @param int|null $year
     * @return Builder
     */
    public function scopeDay($query, string $columnName, ?int $day = null, ?int $month = null, ?int $year = null)
    {     
        $period = TimePeriod::getDayPeriod($day,$month,$year);
           
        return $query->where($columnName,'>',$period['start'])->where($columnName,'<',$period['end']);
    }

    /**
     * Scope day from date
     *
     * @param Builder $query
     * @param integer $date
     * @param string|null $columnName
     * @return Builder
     */
    public function scopeDayFromDate($query, int $date, ?string $columnName = null)
    {
        $period = TimePeriod::getDayPeriod(\date('j',$date),\date('m',$date),\date('Y',$date));
        $columnName = $columnName ?? $this->datePeriodColumnName;

        return $query->where($columnName,'>',$period['start'])->where($columnName,'<',$period['end']);
    }

    /**
     * Scope month from date
     *
     * @param Builder $query
     * @param integer $date
     * @param string|null $columnName
     * @return Builder
     */
    public function scopeMonthFromDate($query, int $date, ?string $columnName = null)
    {
        $columnName = $columnName ?? $this->datePeriodColumnName;
        $period = TimePeriod::getMonthPeriod(\date('m',$date),\date('Y',$date));
        
        return $query->where($columnName,'>',$period['start'])->where($columnName,'<',$period['end']);
    }

    /**
     * Scope year from date
     *
     * @param Builder $query
     * @param integer $date
     * @param string|null $columnName
     * @return Builder
     */
    public function scopeYearFromDate($query, int $date, ?string $columnName = null)
    {
        $columnName = $columnName ?? $this->datePeriodColumnName;
        $period = TimePeriod::getYearPeriod(\date('Y',$date));
        
        return $query->where($columnName,'>',$period['start'])->where($columnName,'<',$period['end']);
    }
}
