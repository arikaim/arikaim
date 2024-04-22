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
use DateTime;

/**
 * Time intervals
 */
class TimeInterval
{
    const TYPE_ISO_8601 = 0;
    const TYPE_CRONTAB  = 1;
    
    /**
     * Interval obj
     *
     * @var object
     */
    private static $interval = null;

    /**
     * Create interval
     *
     * @param mixed $interval
     * @return DateInterval|null
     */
    public static function create($interval)
    {
        if (empty($interval) == true) {
            return null;
        }
        if (Self::isDurationInverval($interval) == true) {
            return new DateInterval($interval);
        }
        if (\is_numeric($interval) == true) {
            return Self::createFromSeconds($interval);
        }
        if (\strtotime($interval) !== false) {
            return DateInterval::createFromDateString($interval);
        }
        
        return null;  
    } 

    /**
     * Create time interval from soconds
     *
     * @param integer $seconds
     * @return DateInterval|null
     */
    public static function createFromSeconds($seconds) 
    {
        if (empty($seconds) == true) {
            return null;
        }
        $start = new DateTime();
        $end = new DateTime();
        $end->add(new DateInterval('PT'. $seconds . 'S'));

        return $end->diff($start);
    }

    /**
     * Return interval object
     *
     * @param mixed $interval
     * @return object
     */
    public static function getDateInterval($interval)
    {
        if (empty(Self::$interval) == true) {
            Self::$interval = Self::create($interval);
        }

        return Self::$interval;
    }

    /**
     * Get years
     *
     * @param mixed|null $interval
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
        $years   = (Self::getYears() > 0) ? Self::getYears() . 'Y' : '';
        $months  = (Self::getMonths() > 0) ? Self::getMonths() . 'M' : '';
        $days    = (Self::getDays() > 0) ? Self::getDays() . 'D' : '';
        $hours   = (Self::getHours() > 0) ? Self::getHours() . 'H' : '';
        $minutes = (Self::getMinutes() > 0) ? Self::getMinutes() . 'M' : '';

        return 'P' . $years . $months . $days . 'T' . $hours . $minutes;        
    }

    /**
     * Convert interval to array
     *
     * @return array
     */
    public static function toArray(): array
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
    public static function isDurationInverval(string $text): bool
    {
        return (\substr($text,0,1) == 'P');   
    }
}
