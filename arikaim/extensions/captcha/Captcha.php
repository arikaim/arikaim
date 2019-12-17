<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Captcha;

use Arikaim\Core\Extension\Extension;

/**
 * Captcha extension
*/
class Captcha extends Extension
{
    /**
     * Install extension routes, events, jobs
     *
     * @return boolean
    */
    public function install()
    {        
        // current captcha driver
        $this->createOption('captcha.current','recaptcha');

        return true;
    }   
}
