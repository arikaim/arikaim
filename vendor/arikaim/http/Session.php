<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Http;

use Arikaim\Core\Collection\Arrays;

/**
 * Session wrapper
 */
class Session 
{      
    /**
     * Default session lifetime value
     *
     * @var integer
     */
    private static $defaultLifetime = 36000;

    /**
     * Start session
     *
     * @param integer|null $lifetime
     * @return void
     */
    public static function start($lifetime = null) 
    {
        $lifetime = ($lifetime == null) ? Self::$defaultLifetime : $lifetime;          
        Self::setLifetime($lifetime);

        if (Self::isStarted() == false) {
            session_start();
            $startTime = Self::getStartTime();
            $startTime = (empty($startTime) == true) ? time() : $startTime;
            Self::set('time_start',$startTime);  
            Self::set('lifetime',$lifetime);          
        }

        if (Self::isActive() == false) {
            session_cache_limiter(false);  
        }      
    }

    /**
     * Return true if session is started
     *
     * @return boolean
     */
    public static function isStarted()
    {
        return !(session_status() == PHP_SESSION_NONE);
    }

    /**
     * Return true if session is active
     *
     * @return boolean
     */
    public static function isActive() 
    {
        return (session_status() == PHP_SESSION_ACTIVE);
    }

    /**
     * Urecreate session
     *
     * @param integer $lifetime
     * @return bool
     */
    public static function recrete($lifetime = null) 
    {
        $session = Self::toArray();
        Self::start($lifetime);

        foreach ($session as $key => $value) {
            Self::set($key,$value);
        }
        Self::set('time_start',time());   

        return true;
    }

    /**
     * Get session start time
     *
     * @return integer
     */
    public static function getStartTime()
    {
        return Self::get('time_start');
    }

    /**
     * Get session end time.
     *
     * @return integer
     */
    public static function getEndTime()
    {   
        return Self::getStartTime() + Self::getLifetime();
    }

    /**
     * Set session lifetime
     *
     * @param integer $time
     * @return void
     */
    public static function setLifetime($time)
    {
        ini_set("session.cookie_lifetime",$time);
        ini_set("session.gc_maxlifetime",$time);
        session_set_cookie_params($time);
    }

    /**
     * Return session lifetime
     *
     * @return integer
     */
    public static function getLifetime()
    {
        $info = session_get_cookie_params();

        return $info['lifetime'];
    }

    /**
     * Get session Id
     *
     * @return string
     */
    public static function getId() 
    {
        $id = session_id();

        return $id;  
    }
    
    /**
     * Get session params
     *
     * @return array
     */
    public static function getParams() 
    {
        return [            
            'time_start'  => Self::getStartTime(),
            'time_end'    => Self::getEndTime(),
            'lifetime'    => Self::getLifetime(),
            'active'      => Self::isActive(),
            'started'     => Self::isStarted(),
            'use_cookies' => Self::isUseCookies()
        ];
    }

    /**
     * Set value
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function set($name, $value) 
    {
        $_SESSION[$name] = $value;
    }
    
    /**
     * Return session value or default value if session variable missing
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        return (isset($_SESSION[$name]) == true) ? $_SESSION[$name] : $default;
    }
    
    /**
     * Return sesion var by path
     *
     * @param string $path
     * @return mixed
     */
    public static function getValue($path)
    {
        return Arrays::getValue($_SESSION,$path);        
    }
    
    /**
     * Remove session value
     *
     * @param string $name
     * @return void
     */
    public static function remove($name) 
    {
        unset($_SESSION[$name]);
    }
    
    /**
     * Destroy session
     * 
     * @param boolean $destoryCookie
     * @return void
     */
    public static function destroy($destoryCookie = true)
    {
        if ($destoryCookie == true) {
            setcookie(session_id(),"",time() - 3600);
        }       
        session_destroy();
    }

    /**
     * Clear all session varibales and start new sesion
     * 
     * @param integer|null $lifetime
     * @return void
     */
    public static function restart($lifetime = null)
    {
        session_unset();      
        Self::destroy();
        Self::start($lifetime);
    }

    /**
     * Get session status
     *
     * @return integer
     */
    public static function getStatus()
    {
        return session_status();
    }

    /**
     * Get session array 
     *
     * @return array
     */
    public static function toArray()
    {
        return (is_array($_SESSION) == true) ? $_SESSION : [];          
    }

    /**
     * Return true if session is stored in cookies
     *
     * @return boolean
     */
    public static function isUseCookies() {
        return ini_get("session.use_cookies");
    }
}
