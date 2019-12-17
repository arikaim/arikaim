<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Recaptcha;

use Arikaim\Core\Extension\Module;

/**
 * Recaptcha module class
 */
class ReCaptcha extends Module
{   
    
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Install module
     *
     * @return void
     */
    public function install()
    {
        $this->installDriver('Arikaim\\Modules\\Recaptcha\\RecaptchaDriver');
        return true;
    }
}
