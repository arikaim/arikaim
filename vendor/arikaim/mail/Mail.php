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

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

use Arikaim\Core\Mail\Interfaces\MailInterface;
use Arikaim\Core\Interfaces\MailerInterface;
use Exception;

/**
 * Mail base class
 */
class Mail implements MailInterface
{ 
    const HTML_CONTENT_TYPE = 'text/html';
    const PLAIN_CONTENT_TYPE = 'text/plain';

    /**
     * Message
     *
     * @var Symfony\Component\Mime\Email
     */
    protected $message;

    /**
     * Mailer
     *
     * @var MailerInterface
     */
    private $mailer;

    /**
     * Email content type
     *
     * @var string
     */
    protected $contentType = Self::PLAIN_CONTENT_TYPE;

    /**
     *  Errors
     *  
     *  @var array
     */
    protected $errors = [];

    /**
     * Constructor
     *
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->contentType = Self::PLAIN_CONTENT_TYPE;
        $this->mailer = $mailer;
        $this->message = new Email();
        $this->errors = [];
    } 

    /**
     * Get errors
     *
     * @return array
     */
    public function getErros(): array
    {
        return $this->errors;
    }

    /**
     * Has errors
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return (count($this->errors) > 0);
    }

    /**
     * Create mail
     *
     * @param MailerInterface $mailer
     * @return Self
     */
    public static function create(MailerInterface $mailer): object
    {
        return new Self($mailer);
    }

    /**
     * Build email
     *
     * @return Self
     */
    public function build(): object
    {
        return $this;
    }
  
    /**
     * Set email subject
     *
     * @param string $subject
     * @return Self
     */
    public function subject(string $subject): object
    {
        $this->message->subject($subject);

        return $this;
    }

    /**
     * Attach file
     *
     * @param string $path
     * @param string $file
     * @param string $contentType
     * @return Self
     */
    public function attach(string $path, string $name = null, string $contentType = null): object
    {      
        $this->message->attachFromPath($path,$name,$contentType);

        return $this;
    }

    /**
     * Set from
     *
     * @param string $email
     * @param string|null $name
     * @return Self
     */
    public function from($email, ?string $name = null): object
    {
        try {
            $email = \trim($email ?? '');
            $address = (empty($name) == false) ? new Address($email,$name) : $email;
            $this->message->from($address);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
       
        return $this;
    } 

    /**
     *  Get from address  email, name
     */
    public function getFrom()
    {
        return $this->message->getFrom();
    }

    /**
     * Set to
     *
     * @param string $email
     * @param string|null $name
     * @return Self
     */
    public function to($email, ?string $name = null): object
    {        
        try {
            $email = \trim($email ?? '');

            $address = (empty($name) == false) ? new Address($email,$name) : $email;
            $this->message->to($address);   
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }

        return $this;
    }

    /**
     * Set reply to
     *
     * @param string $email
     * @param string|null $name
     * @return Self
     */
    public function replyTo($email, ?string $name = null): object
    {
        try {
            $email = \trim($email ?? '');
            $address = (empty($name) == false) ? new Address($email,$name) : $email;
            $this->message->replyTo($address);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }

        return $this;
    }

    /**
     * Set cc
     *
     * @param string $email
     * @param string|null $name
     * @return Self
     */
    public function cc($email, ?string $name = null): object
    {
        try {
            $email = \trim($email ?? '');
            $address = (empty($name) == false) ? new Address($email,$name) : $email;
            $this->message->cc($address);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }

        return $this;
    }

    /**
     * Set bcc
     *
     * @param string $email
     * @param string|null $name
     * @return Self
     */
    public function bcc($email, ?string $name = null): object
    {
        try {
            $email = \trim($email ?? '');
            $address = (empty($name) == false) ? new Address($email,$name) : $email;
            $this->message->bcc($address);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }

        return $this;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return Self
     */
    public function priority(int $priority = 3): object
    {
        $this->message->priority($priority);

        return $this;
    }
    
    /**
     * Set email body
     *
     * @param string $message
     * @return Self
     */
    public function message(string $message, ?string $contentType = null): object
    {
        if (empty($contentType) == true) {
            // detect 
            $this->contentType = ($message != \strip_tags($message)) ? Self::HTML_CONTENT_TYPE : Self::PLAIN_CONTENT_TYPE;              
        }

        if ($this->contentType == Self::HTML_CONTENT_TYPE) {
            $this->message->html($message);
        } else {
            $this->message->text($message);
        }

        return $this;
    }

    /**
     * Set email content type
     *
     * @param string $type
     * @return Self
     */
    public function contentType(string $type = Self::PLAIN_CONTENT_TYPE): object
    {
        $this->contentType = $type;
    
        return $this;
    }

    /**
     * Return message body
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->message->getBody();
    }

    /**
     * Get message instance
     *
     * @return Symfony\Component\Mime\Email
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Send email
     *
     * @return bool
     */
    public function send(): bool 
    {
        return $this->mailer->send($this);
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->mailer->getErrorMessage();
    }
}
