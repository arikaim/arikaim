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
 * Mailer driver interface
 */
interface MailerDriverInterface
{   
    /**
     * Get mailer trasport adapter
     *
     * @return \Symfony\Component\Mailer\Transport
     */
    public function getMailerTransport();

    /**
     * Return driver name.
     *
     * @return string|null
     */
    public function getDriverName(): ?string;
}
