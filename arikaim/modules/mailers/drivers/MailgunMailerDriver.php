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
 * Mailgun  Mailer Driver class
 */
class MailgunMailerDriver implements DriverInterface, MailerDriverInterface
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
        $this->setDriverParams('mailgun-mailer','mailers','Mailgun mailer','Mailgun mailer driver');        
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
        $dns = 'mailgun+https://' . $config['api_key'] . ':' . $config['domain'] . '@default';

        $this->transport = Transport::fromDsn($dns);          
    }

    /**
     * Create driver config properties array
     *
     * @param Arikaim\Core\Collection\Properties $properties
     * @return void
     */
    public function createDriverConfig($properties)
    {            
        // api key
        $properties->property('api_key',function($property) {
            $property
                ->title('Api Key')
                ->type('text')               
                ->required(false)    
                ->default('');           
        });
        
        // Domain
        $properties->property('domain',function($property) {
            $property
                ->title('Domain')
                ->type('text')               
                ->required(false)    
                ->default('');           
        });
    }
}
