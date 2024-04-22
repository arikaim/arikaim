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
 * Mailjet Mailer Driver class
 */
class MailjetMailerDriver implements DriverInterface, MailerDriverInterface
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
        $this->setDriverParams('mailjet-mailer','mailers','Mailjet mailer','Mailjet mailer driver');        
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
        $smtp = (bool)$config['smtp'] ?? false;
        $sandbox = (bool)$config['sandbox'] ?? false;

        if ($smtp == true) {
            $dns = 'mailjet+smtp://' . $config['access_key'] . ':' . $config['secret_key'] . '@default';
        } else {
            $dns = 'mailjet+api://' . $config['access_key'] . ':' . $config['secret_key'] . '@default';
        }
       
        if ($sandbox == true) {
            $dns .= '?sandbox=true';
        }

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
        // access key
        $properties->property('access_key',function($property) {
            $property
                ->title('Access Key')
                ->type('text')               
                ->required(true)    
                ->default('');           
        });
        
        // secret key
        $properties->property('secret_key',function($property) {
            $property
                ->title('Secret Key')
                ->type('text')               
                ->required(true)    
                ->default('');           
        });

        // Sandbox
        $properties->property('sandbox',function($property) {
            $property
                ->title('Sandbox')
                ->type('boolean')               
                ->required(false)    
                ->default(false);           
        });

        // SMTP 
        $properties->property('smtp',function($property) {
            $property
                ->title('Use SMTP')
                ->type('boolean')               
                ->required(false)    
                ->default(false);           
        });
    }
}
