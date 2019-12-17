<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Mail;

use Arikaim\Core\Mail\Interfaces\MailInterface;
use Arikaim\Core\Interfaces\MailerInterface;
use Arikaim\Core\Interfaces\OptionsInterface;
use Arikaim\Core\Interfaces\View\HtmlPageInterface;
use Arikaim\Core\Mail\Mail;

/**
 * Send emails
 */
class Mailer implements MailerInterface
{
    /**
     * Mailer object
     *
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * Mailer error message
     *
     * @var string
     */
    private $error;

    /**
     * Options storage
     *
     * @var OptionsInterface
     */
    private $options;

    /**
     * Constructor
     * 
     * @param \Swift_Transport $transportDriver
     */
    public function __construct(OptionsInterface $options, HtmlPageInterface $page = null, $transportDriver = null) 
    {
        $this->error = null;
        $this->options = $options;
        $this->page = $page;

        if ($transportDriver == null) {
            $transport = $this->createDefaultTransportDriver();
        }
        $this->mailer = new \Swift_Mailer($transport);
    }

    /**
     * Return options
     *
     * @return OptionsInterface
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Create message
     *
     * @return MailInterface
     */
    public function create()
    {
        return new Mail($this,$this->page);
    }

    /**
     * Create default transport driver
     *
     * @return \Swift_Transport
     */
    private function createDefaultTransportDriver()
    {
        if ($this->options->get('mailer.use.sendmail') === true) {
            $transport = new \Swift_SendmailTransport('/usr/sbin/sendmail -bs');
        } else {           
            $transport = new \Swift_SmtpTransport($this->options->get('mailer.smpt.host'),$this->options->get('mailer.smpt.port'));
            $transport->setUsername($this->options->get('mailer.username'));
            $transport->setPassword($this->options->get('mailer.password'));   
           
            if ($this->options->get('mailer.smpt.ssl') == true) {
                $transport->setEncryption('ssl');    
            }              
        }

        return $transport;
    }

    /**
     * Send email
     *
     * @param MailInterface $message
     * @return bool
     */
    public function send($message)
    {
        $this->error = null;

        $message->build();
        $mail = $message->getMessage();

        try {
            $result = $this->mailer->send($mail);
        } catch (\Exception $e) {
            //throw $th;
            $this->error = $e->getMessage();
            $result = false;
        }
        return ($result > 0) ? true : false;
    }

    /**
     * Get mailer transport
     *
     * @return \Swift_Transport
     */
    public function getTransport()
    {
        return $this->mailer->getTransport();
    }

    /**
     * Set transport driver
     *
     * @param \Swift_Transport $driver
     * @return Swift_Mailer
     */
    public function setTransport($driver)
    {
        return $this->mailer = new \Swift_Mailer($driver);
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getErrorMessage()
    {
        return $this->error;
    }    
}