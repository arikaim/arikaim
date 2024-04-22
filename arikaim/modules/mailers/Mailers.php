<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Mailers;

use Arikaim\Core\Extension\Module;

/**
 * Mailers module class
 */
class Mailers extends Module
{  
    /**
     * Install module
     *
     * @return void
     */
    public function install()
    {
        $this->installDriver('Arikaim\\Modules\\Mailers\\Drivers\\SendmailDriver');
        $this->installDriver('Arikaim\\Modules\\Mailers\\Drivers\\SmtpMailerDriver'); 
        $this->installDriver('Arikaim\\Modules\\Mailers\\Drivers\\GmailMailerDriver');      
        $this->installDriver('Arikaim\\Modules\\Mailers\\Drivers\\SendgridMailerDriver');        
        $this->installDriver('Arikaim\\Modules\\Mailers\\Drivers\\MailgunMailerDriver');   
        $this->installDriver('Arikaim\\Modules\\Mailers\\Drivers\\MailjetMailerDriver');        
    }
}
