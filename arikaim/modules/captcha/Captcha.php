<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Captcha;

use Arikaim\Core\Extension\Module;

/**
 * Captcha module class
 */
class Captcha extends Module
{   
    /**
     * Install module
     *
     * @return void
     */
    public function install()
    {
        $this->installDriver('Arikaim\\Modules\\Captcha\\Drivers\\RecaptchaDriver');
    }
}
