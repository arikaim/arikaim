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
use Arikaim\Core\Interfaces\View\HtmlPageInterface;
use Arikaim\Core\Utils\Utils;

/**
 * Mail base class
 */
class Mail implements MailInterface
{ 
    /**
     * Message
     *
     * @var Swift_Message
     */
    protected $message;

    /**
     * Mailer
     *
     * @var MailerInterface
     */
    private $mailer;

    /**
     * Html page
     *
     * @var HtmlPageInterface
     */
    private $page;

    /**
     * Constructor
     */
    public function __construct(MailerInterface $mailer, HtmlPageInterface $page = null)
    {
        $this->mailer = $mailer;
        $this->page = $page;
        $this->message = new \Swift_Message();
        $this->setDefaultFrom();
    } 

    /**
     * Set default from field
     *    
     * @return Mail
     */
    public function setDefaultFrom()
    {
        $from = $this->mailer->getOptions()->get('mailer.from.email',null);
        $fromName = $this->mailer->getOptions()->get('mailer.from.name',null);
        if (empty($from) == false) {
            $this->from($from,$fromName);
        }

        return $this;
    }

    /**
     * Create mail
     *
     * @return Mail
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Send email to 
     *
     * @param string|array $email
     * @param string|null $name
     * @return bool
     */
    public static function sendTo($email, $name = null)
    {
        $mail = new static();
        return $mail->to($email,$name)->send();
    }

    /**
     * Build email
     *
     * @return Mail
     */
    public function build()
    {
        return $this;
    }
  
    /**
     * Set email subject
     *
     * @param string $subject
     * @return Mail
     */
    public function subject($subject)
    {
        $this->message->setSubject($subject);

        return $this;
    }

    /**
     * Attach file
     *
     * @param string $file
     * @return Mail
     */
    public function attach($file)
    {
        $attachment = Swift_Attachment::fromPath($file);
        $this->message->attach($attachment);

        return $this;
    }

    /**
     * Set from
     *
     * @param string|array $email
     * @param string|null $name
     * @return Mail
     */
    public function from($email, $name = null)
    {
        $this->message->setFrom($email,$name);
        return $this;
    } 

    /**
     * Set to
     *
     * @param string|array $email
     * @param string|null $name
     * @return Mail
     */
    public function to($email, $name = null)
    {        
        $this->message->setTo($email,$name);   

        return $this;
    }

    /**
     * Set reply to
     *
     * @param string|array $email
     * @param string|null $name
     * @return Mail
     */
    public function replyTo($email, $name = null)
    {
        $this->message->setReplyTo($email,$name);

        return $this;
    }

    /**
     * Set cc
     *
     * @param string|array $email
     * @param string|null $name
     * @return Mail
     */
    public function cc($email, $name = null)
    {
        $this->message->setCc($email,$name);

        return $this;
    }

    /**
     * Set bcc
     *
     * @param string|array $email
     * @param string|null $name
     * @return Mail
     */
    public function bcc($email, $name = null)
    {
        $this->message->setBcc($email,$name);

        return $this;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return Mail
     */
    public function priority($priority = 3)
    {
        $this->message->setPriority($priority);

        return $this;
    }
    
    /**
     * Set email body
     *
     * @param string $message
     * @return Mail
     */
    public function message($message)
    {
        $this->message->setBody($message);
        return $this;
    }

    /**
     * Set email content type
     *
     * @param string $type
     * @return Mail
     */
    public function contentType($type = "text/plain")
    {
        $this->message->setContentType($type);

        return $this;
    }

    /**
     * Return message body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->message->getBody();
    }

    /**
     * Get message instance
     *
     * @return Swift_Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Load email message from html component
     *
     * @param string $componentName
     * @param array $params
     * @return object
     */
    public function loadComponent($componentName, $params = [])
    {
        if (is_object($this->page) == false) {
            return $this;
        }

        $component = $this->page->createHtmlComponent($componentName,$params,null,false)->renderComponent();
        $properties = $component->getProperties();
        $body = $component->getHtmlCode();

        $this->message($body);

        if (Utils::hasHtml($body) == true) {
            $this->contentType('text/html');
        } else {
            $this->contentType('text/plain');
        }
        
        // subject
        $subject = (isset($properties['subject']) == true) ? $properties['subject'] : "";
        if (empty($subject) == false) {
            $this->subject($subject);
        }
    
        return $this;
    }

    /**
     * Send email
     *
     * @return bool
     */
    public function send() 
    {
        return $this->mailer->send($this);
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getError()
    {
        return $this->mailer->getErrorMessage();
    }
}
