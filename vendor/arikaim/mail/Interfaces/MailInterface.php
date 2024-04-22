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
    public function build(): object;

    /**
     * Get Email message instance
     *
     * @return Symfony\Component\Mime\Email
     */
    public function getMessage();

    /**
     *  Get from address  email, name
     */
    public function getFrom();

    /**
     * Set from
     *
     * @param string|array $email
     * @param string|null $name
     * @return Self
     */
    public function from($email, ?string $name = null): object;
}
