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

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer as SymfonyMailer;

use Arikaim\Core\Mail\Mail;
use Arikaim\Core\Utils\Utils;

use Arikaim\Core\Mail\Interfaces\MailInterface;
use Arikaim\Core\Interfaces\MailerInterface;
use Arikaim\Core\Interfaces\View\EmailViewInterface;
use Arikaim\Core\Mail\Interfaces\MailerDriverInterface;
use Arikaim\Core\Interfaces\LoggerInterface;

use Arikaim\Core\Logger\Traits\LoggerTrait;
use Exception;

/**
 * Send emails
 */
class Mailer implements MailerInterface
{
    use LoggerTrait;

    const LOG_ERROR_MESSAGE = 'Error send email.';
    const LOG_INFO_MESSAGE  = 'Email send successful.';

    /**
     * Mailer object
     *
     * @var Symfony\Component\Mailer\Mailer
     */
    private $mailer;

    /**
     * Mailer error message
     *
     * @var string|null
     */
    private $error = null;

    /**
     * Options
     *
     * @var array
     */
    private $options = [];
 
    /**
     * Email component renderer
     *
     * @var EmailViewInterface|null
     */
    private $view;
  
    /**
     * Driver name
     *
     * @var string|null
     */
    private $driverName = 'sendmail';

    /**
    * Constructor
    *
    * @param array $options
    * @param HtmlComponentInterface $renderer
    */
    public function __construct(
        array $options, 
        EmailViewInterface $view, 
        ?MailerDriverInterface $driver = null,
        ?LoggerInterface $logger = null      
    ) 
    {
        $this->options = $options;
        $this->view = $view;
        $this->setLogger($logger);

        if (empty($driver) == true) {
            $transport = Self::crateSendmailTranspart();           
        } else {
            $transport = $driver->getMailerTransport();
            $this->driverName = $driver->getDriverName();
        }
              
        $this->error = null;
        $this->mailer = new SymfonyMailer($transport);
    }

    /**
     * Create sendmail transport
     *
     * @return object
     */
    public static function crateSendmailTranspart()
    {
        return Transport::fromDsn('sendmail://default');       
    }

    /**
     * Return options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get option value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Create message
     *
     * @param string|null $componentName
     * @param array $params
     * @param string|null language
     * @return MailInterface|null
     */
    public function create(?string $componentName = null, array $params = [], ?string $language = null)
    {
        $mail = new Mail($this);

        if (empty($componentName) == false) {
            $component = $this->view->render($componentName,$params,$language);
            if ($component == null) {
                throw new Exception('Email component render error ' . $componentName,1);
                return null;
            }
            
            if (empty($component->getSubject()) == false) {
                $mail->subject($component->getSubject());
            }
            // set body
            $body = $component->getHtmlCode();
            $mail->message($body);
            // content type
            $contentType = (Utils::hasHtml($body) == true) ? 'text/html' : 'text/plain';
            $mail->contentType($contentType);
        }

        return $mail;        
    }

    /**
     * Get from email option
     *
     * @return string
     */
    public function getFromEmail(): string
    {
        return $this->options['from_email'] ?? '';
    } 

    /**
     * Get from name option
     *
     * @return string
     */
    public function getFromName(): string
    {
        return $this->options['from_name'] ?? '';
    }
   
    /**
     * Send email
     *
     * @param MailInterface $message
     * @return bool
     */
    public function send($message): bool
    {
        $this->error = null;

        if (empty($message->getFrom()) == true) {
            $message->from($this->getFromEmail(),$this->getFromName());
        }

        $message->build();
        $mail = $message->getMessage();

        try {
            $this->mailer->send($mail);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            if ($this->getOption('log_error',false) == true) {
                $this->logError(Self::LOG_ERROR_MESSAGE,[
                    'error'  => $this->error,
                    'driver' => $this->driverName
                ]);
            }

            return false;
        }
               
        if ($this->getOption('log',false) == true) {  
            $this->logInfo(Self::LOG_INFO_MESSAGE,[
                'driver' => $this->driverName
            ]);
        }
        
        return true;       
    }

    /**
     * Get mailer transport
     *
     * @return Symfony\Component\Mailer\Transport
     */
    public function getTransport()
    {
        return $this->mailer->getTransport();
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->error;
    }    
}