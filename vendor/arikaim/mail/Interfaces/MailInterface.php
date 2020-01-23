<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Mail\Interfaces;

/**
 * Mail interface
 */
interface MailInterface
{   
    /**
     * Build email
     *
     * @return MailInterface
     */ 
    public function build();

    /**
     * Get Swift_Message message instance
     *
     * @return Swift_Message
     */
    public function getMessage();
}
