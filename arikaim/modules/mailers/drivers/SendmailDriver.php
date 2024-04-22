<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Mailers\Drivers;

use Symfony\Component\Mailer\Transport;

use Arikaim\Core\Driver\Traits\Driver;
use Arikaim\Core\Interfaces\Driver\DriverInterface;
use Arikaim\Core\Mail\Interfaces\MailerDriverInterface;

/**
 * Sendmail class
 */
class SendmailDriver implements DriverInterface, MailerDriverInterface
{   
    use Driver;
   
    /**
     * Transport adapter
     *
     * @var Swift_Transport
     */
    protected $transport;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDriverParams('sendmail','mailers','Sendmail mailer','Sendmail mailer driver');        
    }

    /**
     * Get mailer trasport adapter
     *
     * @return Swift_Transport
     */
    public function getMailerTransport()
    {
        return $this->transport;
    }

    /**
     * Init driver
     *
     * @param Properties $properties
     * @return void
     */
    public function initDriver($properties)
    {     
        $config = $properties->getValues(); 
        $this->transport = Transport::fromDsn('sendmail://default'); 
    }

    /**
     * Create driver config properties array
     *
     * @param Arikaim\Core\Collection\Properties $properties
     * @return void
     */
    public function createDriverConfig($properties)
    {            
        $properties->property('dns',function($property) {
            $property
                ->title('Email transport Dns')
                ->type('text')
                ->default('sendmail://default')             
                ->readonly(true)              
                ->value('sendmail://default');           
        });
    }
}
