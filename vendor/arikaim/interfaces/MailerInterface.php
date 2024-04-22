<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces;

/**
 * Mail interface
 */
interface MailerInterface
{   
    /**
     * Send email
     *
     * @param Arikaim\Core\Interfaces\MailInterface $message 
     * @return bool
     */ 
    public function send($message): bool;

     /**
     * Get error message
     *
     * @return string|null
     */
    public function getErrorMessage(): ?string;

    /**
     * Return mailer options
     *
     * @return array
     */
    public function getOptions(): array;
}
