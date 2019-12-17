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

use DateTimeZone;
use Exception;

/**
 * DateTime
 */
class DateTime 
{   
    const DEFAULT_DATE_FORMAT = 'Y-m-d';
    const DEFAULT_TIME_FORMAT = 'H:i';

    /**
     * Time zone
     *
     * @var object
     */
    private static $timeZone;

    /**
     * DateTime object
     *
     * @var DateTime
     */
    private static $dateTime;

    /**
     * Date formats list
     *
     * @var array
    */
    private static $dateFormats = [];

    /**
     * Time formats list
     *
     * @var array
    */
    private static $timeFormats = [];

    /**
     * Set date adn time formats
     *
     * @param array $dateFormats
     * @param array $timeFormats
     * @return void
     */
    public static function setFormats(array $dateFormats,array $timeFormats)
    {
        Self::$dateFormats = $dateFormats;
        Self::$timeFormats = $timeFormats;
    }

    /**
     * Set date time obj
     *
     * @param string|null $date
     * @param string|null $format
     * @return void
     */
    public static function setDateTime($date = null, $format = null)
    {        
        Self::$dateTime = Self::create($date,$format);
    }

    /**
     * Create DateTime obj
     *
     * @param string|null $date
     * @param string|null $format
     * @return DateTime
     */
    public static function create($date = null, $format = null)
    {
        $date = (empty($date) == true) ? 'now' : $date;
        $format = Self::getDateFormat($format);

        $dateTime = new \DateTime($date,Self::getTimeZone());
        $dateTime->format($format);

        return $dateTime;
    }

    /**
     * Get DateTime
     *
     * @return DateTime
     */
    public static function getDateTime()
    {
        if (empty(Self::$dateTime) == true) {
            Self::setDateTime();
        }

        return Self::$dateTime;
    }

    /**
     * Get timestamp
     *
     * @return integer
     */
    public static function getTimestamp()
    {
        return Self::getDateTime()->getTimestamp();
    }

    /**
     * Comvert date time to timestamp
     *
     * @param string|null $date
     * @param string|null $format
     * @return integer
     */
    public static function toTimestamp($date = null, $format = null)
    {
        return Self::create($date,$format)->getTimestamp();    
    }

    /**
     * Get date format
     *
     * @param string|null $name
     * @return string
     */
    public static function getDateFormat($name = null) 
    {      
        if ($name == null) { 
            return Self::DEFAULT_DATE_FORMAT;                  
        }

        return (isset(Self::$dateFormats[$name]) == true) ? Self::$dateFormats[$name] : Self::DEFAULT_DATE_FORMAT;
    }

    /*
    * Get time zone list
    *
    * @return array
    */
    public static function getTimeZonesList()
    {
        return timezone_identifiers_list();
    }

    /**
    * Get location
    *
    * @return string
    */
    public static function getLocation() 
    {
        return Self::getTimeZone()->getLocation();
    }

    /**
    * Get time zone offset
    *
    * @param  DateTime|null $dateTime
    * @return string
    */
    public static function getTimeZoneOffset($dateTime = null) 
    {
        $dateTime = (empty($dateTime) == true) ? Self::$dateTime : $dateTime;

        return Self::getTimeZone()->getOffset($dateTime);
    }

    /**
    * Get time zone
    *
    * @return string
    */
    public static function getTimeZoneName() 
    {
        return Self::getTimeZone()->getName();
    }

    /**
     * Get time zone
     *
     * @return DateTimeZone
     */
    public static function getTimeZone()
    {
        if (empty(Self::$timeZone) == true) {
            Self::setTimeZone();
        }

        return Self::$timeZone;
    }

    /**
     * Return true if time zone is vlaid
     *
     * @param string $name
     * @return boolean
     */
    public static function isValidTimeZone($name) {
        return in_array($name, timezone_identifiers_list());
    }

    /**
     * Set time zone
     *
     * @param string|null $name
     * @throws Exception
     * @return void
     */
    public static function setTimeZone($name = null)
    {
        $name = (empty($name) == true) ? date_default_timezone_get() : $name;
        if (Self::isValidTimeZone($name) == false) {
            throw new Exception('Not vlaid timezone ');
        }
        Self::$timeZone = new DateTimeZone($name);
    }

    /**
     * Return formated timestsamp with current date and time format
     *
     * @param integer $timestamp
     * @param string|null $format
     * @return string
     */
    public static function dateTimeFormat($timestamp, $format = null)
    {
        if (is_numeric($timestamp) == false) {
            return $timestamp;
        }
        if ($format == null) {           
            $format = Self::getDateFormat() . " " . Self::getTimeFormat();
        }
        $date = Self::setTimestamp($timestamp);

        return $date->format($format);     
    }

    /**
     * Return formated time
     *
     * @param integer $timestamp
     * @param string $format
     * @return string
     */
    public static function timeFormat($timestamp, $format = null)
    {
        if (is_numeric($timestamp) == false) {
            return $timestamp;
        }
        $date = Self::setTimestamp($timestamp);

        return $date->format(Self::getTimeFormat($format)); 
    }

    /**
     * Return formated date
     *
     * @param integer $timestamp
     * @param string $format
     * @return string
     */
    public static function dateFormat($timestamp, $format = null)
    {
        if (is_numeric($timestamp) == false) {
            return $timestamp;
        }
        $date = Self::setTimestamp($timestamp);

        return $date->format(Self::getDateFormat($format));
    }

    /**
     * Get time format
     *
     * @param string $name
     * @return string
     */
    public static function getTimeFormat($name = null) 
    {       
        if ($name == null) {
            return Self::DEFAULT_TIME_FORMAT;           
        }

        return (isset(Self::$timeFormats[$name]) == true) ? Self::$timeFormats[$name] : Self::DEFAULT_TIME_FORMAT; 
    }

    /**
     * Get interval details
     *
     * @param string $intervalText
     * @return array
     */
    public static function getInterval($intervalText)
    {
        $interval = new TimeInterval($intervalText);

        return $interval->toArray();
    }
 
    /**
     * Set date format.
     *
     * @param string $dateFormat
     * @return DateTime
     */
    public static function setDateFormat($dateFormat) 
    {
        Self::$dateTime = Self::getDateTime()->format($dateFormat);

        return Self::$dateTime;
    }

    /**
     * Modify date time
     *
     * @param string $date_text
     * @return DateTime
     */
    public static function modify($date_text) 
    {
        Self::$dateTime = Self::getDateTime()->modify($date_text);

        return Self::$dateTime;
    }

    /**
     * Add interval
     *
     * @param string $dateInterval
     * @return DateTime
     */
    public static function addInterval($dateInterval)
    {
        $interval = new \DateInterval($dateInterval); 

        return Self::getDateTime()->add($interval); 
    }
    
    /**
     * Sub interval
     *
     * @param string $dateInterval
     * @return DateTime
     */
    public static function subInterval($dateInterval)
    {
        $interval = new \DateInterval($dateInterval); 

        return Self::getDateTime()->sub($interval);         
    }

    /**
     * Set timestamp
     *
     * @param integer $unixTimestamp
     * @return DateTime
     */
    public static function setTimestamp($unixTimestamp) 
    {
        Self::$dateTime = Self::getDateTime()->setTimestamp($unixTimestamp);

        return Self::$dateTime;
    }

    /**
     * Get curent year
     *
     * @return string
     */
    public static function getYear()
    {
        return date('Y',Self::toTimestamp());
    }

    /**
     * Get current month
     *
     * @return string
     */
    public static function getMonth()
    {
        return date('n',Self::getTimestamp());
    }
    
    /**
     * Return current day
     *
     * @return string
     */
    public static function getDay()
    {
        return date('j',Self::getTimestamp());
    }

    /**
     * Return current hour
     *
     * @return string
     */
    public static function getHour()
    {
        return date('G',Self::getTimestamp());
    }

    /**
     * Get current minutes
     *
     * @return integer
     */
    public static function getMinutes()
    {
        return intval(date('i',Self::getTimestamp()));
    }

    /**
     * Convert current date time to string.
     *
     * @param string $format
     * @return string
     */
    public static function toString($format = null) 
    {
        return Self::getDateTime()->format(Self::getDateFormat($format));
    }   
}
