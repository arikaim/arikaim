<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Utils;

use Arikaim\Core\Utils\DateTime;

/**
 * Time periods
 */
class TimePeriod
{
    /**
     * Check if timestamp is today
     *        
     * @param int $timestamp
     * @return bool
    */
    public static function isToday(int $timestamp): bool
    {
        $period = Self::getDayPeriod();

        return ($timestamp >= $period['start'] && $timestamp >= $period['end']);
    }

    /**
     * Check if timestamp is in year 
     * 
     * @param int $timestamp          
     * @param int|null $year
     * @return bool
    */
    public static function isYear(int $timestamp, ?int $year = null): bool
    {
        $period = Self::getYearPeriod($year);

        return ($timestamp >= $period['start'] && $timestamp >= $period['end']);
    }

    /**
     * Check if timestamp is in month 
     * 
     * @param int $timestamp       
     * @param int|null $month
     * @param int|null $year
     * @return bool
    */
    public static function isMonth(int $timestamp, ?int $month = null, ?int $year = null): bool
    {
        $period = Self::getMonthPeriod($month,$year);

        return ($timestamp >= $period['start'] && $timestamp >= $period['end']);
    }

    /**
     * Get month period
     *        
     * @param int|null $year
     * @return array
    */
    public static function getYearPeriod(?int $year = null): array
    {
        $year = $year ?? \date('Y');

        return Self::getPeriod($year . '-01-01T00:00:00',$year . '-12-31T23:59:59'); 
    } 

    /**
     * Get month period
     *   
     * @param int|null $month
     * @param int|null $year
     * @return array
     */
    public static function getMonthPeriod(?int $month = null, ?int $year = null): array
    {
        $year = (empty($year) == true) ? \date('Y') : $year;
        $month = (empty($month) == true) ? \date('m') : $month;
        $lastDay = DateTime::getLastDay($month);
        $startDate = $year . '-' . $month . '-01T00:00:00';
        $endDate = $year . '-' . $month . '-' . $lastDay . 'T23:59:59';
        
        return Self::getPeriod($startDate,$endDate);       
    }

    /**
     * Get day period
     *
     * @param int|null $day
     * @param int|null $month
     * @param int|null $year
     * @return array
     */
    public static function getDayPeriod(?int $day = null, ?int $month = null, ?int $year = null): array
    {
        $day = (empty($day) == true) ? \date('j') : $day;
        $year = (empty($year) == true) ? \date('Y') : $year;
        $month = (empty($month) == true) ? \date('m') : $month;
        $startDate = $year . '-' . $month . '-' . $day . 'T00:00:00';
        $endDate = $year . '-' . $month . '-' . $day . 'T23:59:59';

        return Self::getPeriod($startDate,$endDate);      
    }

    /**
     * Get yesterday time period
     *
     * @return array
     */
    public static function getYesterdayPeriod(): array
    {
        return Self::getDayPeriod(\date('m'),\date('j') - 1,\date('Y'));
    }

    /**
     * Get period
     *
     * @param string $fromDate
     * @param string $toDate
     * @return array
     */
    public static function getPeriod(string $fromDate, string $toDate): array
    {
        return [
            'start' => DateTime::toTimestamp($fromDate,DateTime::ISO8601ZULU_FORMAT),
            'end'   => DateTime::toTimestamp($toDate,DateTime::ISO8601ZULU_FORMAT)
        ];
    }
}
