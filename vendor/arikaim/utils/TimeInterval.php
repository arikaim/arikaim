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

use DateInterval;

/**
 * Time intervals
 */
class TimeInterval
{
    /**
     * Interval obj
     *
     * @var object
     */
    private static $interval = null;

    /**
     * Create interval
     *
     * @param string $interval
     * @return DateInterval
     */
    public static function create($interval = "")
    {
        return (Self::isDurationInverval($interval) == true) ? new DateInterval($interval) : DateInterval::createFromDateString($interval);       
    } 

    /**
     * Return interval object
     *
     * @param string $interval
     * @return object
     */
    public static function getDateInterval($interval = "")
    {
        if (empty(Self::$interval) == true) {
            Self::$interval = Self::create($interval);
        }

        return Self::$interval;
    }

    /**
     * Get years
     *
     * @param string|null $interval
     * @return integer
     */
    public static function getYears($interval = null)
    {
        return Self::getDateInterval($interval)->y;
    }

    /**
     * Get months
     *
     * @param string|null $interval
     * @return integer
     */
    public static function getMonths($interval = null)
    {
        return Self::getDateInterval($interval)->m;
    }

    /**
     * Get hours
     *
     * @param string|null $interval
     * @return integer
     */
    public static function getHours($interval = null)
    {
        return Self::getDateInterval($interval)->h;
    }

    /**
     * Get minutes
     *
     * @param string|null $interval
     * @return integer
     */
    public static function getMinutes($interval = null)
    {
        return Self::getDateInterval($interval)->i;
    }

    /**
     * Get days
     *
     * @param string|null $interval
     * @return integer
     */
    public static function getDays($interval = null)
    {
        return Self::getDateInterval($interval)->d;
    }

    /**
     * Get date time interval as string
     *
     * @return string
     */
    public static function getInterval()
    {
        $years   = (Self::getYears() > 0) ? Self::getYears() . "Y" : "";
        $months  = (Self::getMonths() > 0) ? Self::getMonths() . "M" : "";
        $days    = (Self::getDays() > 0) ? Self::getDays() . "D" : "";
        $hours   = (Self::getHours() > 0) ? Self::getHours() . "H" : "";
        $minutes = (Self::getMinutes() > 0) ? Self::getMinutes() . "M" : "";

        return "P" . $years . $months . $days . "T" . $hours . $minutes;        
    }

    /**
     * Set years
     *
     * @param integer $years
     * @return void
     */
    public static function setYears($years)
    {
        Self::$interval = Self::getDateInterval()->y = $years;
    }

    /**
     * Set months
     *
     * @param integer $months
     * @return void
     */
    public static function setMonths($months)
    {
        Self::$interval = Self::getDateInterval()->m = $months;
    }

    /**
     * Set days
     *
     * @param integer $days
     * @return void
     */
    public static function setDays($days)
    {
        Self::$interval = Self::getDateInterval()->d = $days;
    }

    /**
     * Set hours
     *
     * @param integer $hours
     * @return void
     */
    public static function setHours($hours)
    {
        Self::$interval = Self::getDateInterval()->h = $hours;
    }

    /**
     * Set minutes
     *
     * @param integer $minutes
     * @return void
     */
    public static function setMinutes($minutes)
    {
        Self::$interval = Self::getDateInterval()->i = $minutes;
    }

    /**
     * Convert interval to array
     *
     * @return array
     */
    public static function toArray()
    {
        return [
            'years'     => Self::getYears(),
            'months'    => Self::getMonths(),
            'days'      => Self::getDays(),
            'hours'     => Self::getHours(),
            'minutes'   => Self::getMinutes()
        ];
    }

    /**
     * Return true if text is valid interval string
     *
     * @param string $text
     * @return boolean
     */
    public static function isDurationInverval($text)
    {
        return (substr($text,0,1) == 'P') ? true : false;           
    }
}
