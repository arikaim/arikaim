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
use DateInterval;

/**
 * DateTime
 */
class DateTime 
{   
    const DEFAULT_DATE_FORMAT = 'Y-m-d';
    const DEFAULT_TIME_FORMAT = 'H:i';
    const ISO8601_FORMAT      = DATE_ATOM;
    const RFC3339_FORMAT      = DATE_RFC3339;
    const ISO8601ZULU_FORMAT  = 'Y-m-d\TH:i:s\Z';
    const ICALENDAR_EVENT_UTC = 'Ymd\THis\Z';
    const ICALENDAR_EVENT     = 'Ymd\THis';

    /**
     * Time zone
     *
     * @var object
     */
    private static $timeZone;

    /**
     * DateTime object
     *
     * @var \DateTime
     */
    private static $dateTime;

    /**
     * Date format
     *
     * @var string|null
    */
    private static $dateFormat = null;

    /**
     * Time formats list
     *
     * @var string|null
    */
    private static $timeFormat = null;

    /**
     * Set date format
     * 
     * @param string|null $format
     * @return void
     */
    public static function setDateFormat(?string $format = null): void
    {
        Self::$dateFormat = $format;
    }

    /**
     * Set time formats
     *    
     * @param string|null $format
     * @return void
     */
    public static function setTimeFormat(?string $format = null): void
    {
        Self::$timeFormat = $format;      
    }

    /**
     * Set date time obj
     *
     * @param string|null $date
     * @param string|null $format
     * @return void
     */
    public static function setDateTime(?string $date = null, ?string $format = null): void
    {        
        Self::$dateTime = Self::create($date,$format);
    }

    /**
     * Create DateTime obj
     *
     * @param string|null $date
     * @param string|null $format
     * @return \DateTime
     */
    public static function create(?string $date = null, ?string $format = null): object
    {
        if (empty($date) == true || $date == 'now') {
            $dateTime = new \DateTime('now',Self::getTimeZone());
            $dateTime->format($format ?? Self::getDateFormat());
            return $dateTime;
        }
        
        return \DateTime::createFromFormat(
            $format ?? Self::getDateFormat(),
            $date,
            Self::getTimeZone()
        );
    }

    /**
     * Get DateTime
     *
     * @return \DateTime
     */
    public static function getDateTime(): object
    {
        if (empty(Self::$dateTime) == true) {
            Self::setDateTime();
        }

        return Self::$dateTime;
    }

    /**
     * Get timestamp
     *
     * @return int
     */
    public static function getTimestamp(): int
    {
        return Self::getDateTime()->getTimestamp();
    }

    /**
     * Get current timestamp
     *
     * @return int
     */
    public static function getCurrentTimestamp(): int
    {
        return Self::create()->getTimestamp();
    } 

    /**
     * Create form timestamp
     *
     * @param integer     $timestamp
     * @param string|null $format
     * @return object
     */
    public static function createFromTimestamp(int $timestamp, ?string $format = null): object
    {
        $date = Self::create(null,$format);
        $date->setTimestamp($timestamp);

        return $date;
    }

    /**
     * Comvert date time to timestamp
     *
     * @param string|null $date
     * @param string|null $format
     * @return integer
     */
    public static function toTimestamp(?string $date = null, ?string $format = null): int
    {
        return Self::create($date,$format)->getTimestamp();    
    }

    /**
     * Get date format
     *
     * @return string
     */
    public static function getDateFormat(): string 
    {            
        return Self::$dateFormat ?? \constant('CURRENT_DATE_FORMAT') ?? Self::DEFAULT_DATE_FORMAT;
    }

    /**
    * Get time zone list
    *
    * @return array|false
    */
    public static function getTimeZonesList()
    {
        return \timezone_identifiers_list();
    }

    /**
    * Get location
    *
    * @return mixed
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
    public static function getTimeZoneOffset($dateTime = null): string 
    {
        return Self::getTimeZone()->getOffset($dateTime ?? Self::$dateTime);
    }

    /**
    * Get time zone
    *
    * @return string
    */
    public static function getTimeZoneName(): string 
    {
        return Self::getTimeZone()->getName();
    }

    /**
     * Get time zone
     *
     * @return DateTimeZone|null
     */
    public static function getTimeZone(): ?object
    {
        if (empty(Self::$timeZone) == true) {         
            Self::setTimeZone(\date_default_timezone_get());
        }

        return Self::$timeZone;
    }

    /**
     * Return true if time zone is vlaid
     *
     * @param string|null $name
     * @return boolean
     */
    public static function isValidTimeZone(?string $name): bool 
    {
        return (empty($name) == true) ? false : \in_array($name,\timezone_identifiers_list());
    }

    /**
     * Set time zone
     *
     * @param string|null $name    
     * @return void
     */
    public static function setTimeZone(?string $name): void
    {
        $name = (Self::isValidTimeZone($name) == false || empty($name) == true) ? \date_default_timezone_get() : $name;
        
        Self::$timeZone = new DateTimeZone($name);
    }

    /**
     * Return formated timestsamp with current date and time format
     *
     * @param integer|null $timestamp
     * @param string|null $format
     * @return string|null
     */
    public static function dateTimeFormat(?int $timestamp, ?string $format = null): ?string
    {
        if ($timestamp === null) {
            return null;
        }       
        $format = $format ?? Self::getDateFormat() . ' ' . Self::getTimeFormat();
    
        return Self::setTimestamp($timestamp)->format($format);     
    }

    /**
     * Return formated time
     *
     * @param integer|null $timestamp
     * @param string $format
     * @return string|null
     */
    public static function timeFormat(?int $timestamp, ?string $format = null): ?string
    {
        return (\is_numeric($timestamp) == false) ? $timestamp : 
            Self::setTimestamp($timestamp)->format($format ?? Self::getTimeFormat()); 
    }

    /**
     * Return formated date
     *
     * @param integer|null $timestamp
     * @param string|null $format
     * @return string|null
     */
    public static function dateFormat(?int $timestamp, ?string $format = null): ?string
    {
        return (\is_numeric($timestamp) == false) ? $timestamp : 
            Self::setTimestamp((integer)$timestamp)->format($format ?? Self::getDateFormat());
    }

    /**
     * Get time format
     *     
     * @return string
     */
    public static function getTimeFormat(): string 
    {       
        return Self::$timeFormat ?? \constant('CURRENT_TIME_FORMAT') ?? Self::DEFAULT_TIME_FORMAT;    
    }

    /**
     * Get interval details
     *
     * @param string $intervalText
     * @return array
     */
    public static function getInterval(string $intervalText): array
    {
        return (new TimeInterval($intervalText))->toArray();
    }
 
    /**
     * Modify date time
     *
     * @param string|null $dateText
     * @return \DateTime
     */
    public static function modify(?string $dateText): object
    {
        Self::$dateTime = Self::getDateTime()->modify($dateText);

        return Self::$dateTime;
    }

    /**
     * Add interval
     *
     * @param string $dateInterval
     * @return \DateTime
     */
    public static function addInterval(string $dateInterval): object
    {
        try {
            $interval = DateInterval::createFromDateString($dateInterval);
        } catch (\Exception $e) {
            $interval = new DateInterval($dateInterval);
        }
      
        $interval = ($interval === false) ? new DateInterval($dateInterval) : $interval;
        
        return Self::getDateTime()->add($interval); 
    }
    
    /**
     * Sub interval
     *
     * @param string $dateInterval
     * @return \DateTime
     */
    public static function subInterval(string $dateInterval): object
    {
        $interval = DateInterval::createFromDateString($dateInterval); 
        $interval = ($interval === false) ? new DateInterval($dateInterval) : $interval;
    
        return Self::getDateTime()->sub($interval);         
    }

    /**
     * Set timestamp
     *
     * @param integer $unixTimestamp
     * @return \DateTime
     */
    public static function setTimestamp(int $unixTimestamp): object
    {
        Self::$dateTime = Self::getDateTime()->setTimestamp($unixTimestamp);

        return Self::$dateTime;
    }

    /**
     * Get curent year
     *
     * @return string
     */
    public static function getYear(): string
    {
        return \date('Y',Self::toTimestamp());
    }

    /**
     * Get current year
     *
     * @return string
     */
    public static function getCurrentYear(): string
    {
        return \date('Y');
    }
   
    /**
     * Get last day of month
     *
     * @param integer|string $month
     * @return integer
     */
    public static function getLastDay($month): string
    {
        $date = Self::getYear() . '-' . $month . '-01';
        
        return \date('t',Self::toTimestamp($date,'Y-m-d'));
    }

    /**
     * Get current month
     *
     * @return string
     */
    public static function getMonth(): string
    {
        return \date('n',Self::getTimestamp());
    }
    
    /**
     * Return current day
     *
     * @return string
     */
    public static function getDay(): string
    {
        return \date('j',Self::getTimestamp());
    }

    /**
     * Return current hour
     *
     * @return string
     */
    public static function getHour(): string
    {
        return \date('G',Self::getTimestamp());
    }

    /**
     * Get current minutes
     *
     * @return string
     */
    public static function getMinutes(): string
    {
        return \intval(\date('i',Self::getTimestamp()));
    }

    /**
     * Convert current date time to string.
     *
     * @param string|null $format
     * @return string
     */
    public static function toString(?string $format = null): string 
    {
        return Self::getDateTime()->format($format ?? Self::getDateFormat());
    }   

    /**
     * Get current date
     *
     * @param string|null $format
     * @return \DateTime
     */
    public static function today(?string $format = null): object
    {
        return Self::create('now',$format);
    }

    /**
     * Convert date string to other format
     *
     * @param string $date
     * @param string|null $format
     * @return string|false
     */
    public static function convert($date, ?string $format = null)
    {
        return (\strtotime($date) === false) ? false : 
            Self::create($date)->format($format ?? Self::getDateFormat());
    }
}
