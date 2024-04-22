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
 * Gmail mailer driver class
 */
class GmailMailerDriver implements DriverInterface, MailerDriverInterface
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
        $this->setDriverParams('gmail-mailer','mailers','Gmail mailer','Gmail mailer driver');        
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
        $dns = 'gmail+smtp://' . $config['username'] . ':' . $config['password'] . '@default?verify_peer=0';

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
        // username
        $properties->property('username',function($property) {
            $property
                ->title('Username')
                ->type('text')             
                ->required(true)    
                ->default('');
        });
        // password
        $properties->property('password',function($property) {
            $property
                ->title('Password')
                ->type('password')               
                ->required(true)    
                ->default('');
        });
    }
}
