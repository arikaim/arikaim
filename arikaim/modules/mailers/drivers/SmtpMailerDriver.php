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
 * SmtpMailerDriver class
 */
class SmtpMailerDriver implements DriverInterface, MailerDriverInterface
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
        $this->setDriverParams('smtp-mailer','mailers','Smtp mailer','Smtp mailer driver');        
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
           
        $port = $config['port'] ?? 25;
        $dns = 'smtp://' . $config['username'] . ':' . $config['password'] . '@' . $config['host'] . ':' . $port;

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
        $properties->property('host_group',function($property) {
            $property
                ->title('Host')
                ->type('group')
                ->displayType('segment')
                ->required(true)              
                ->value('host_group');           
        });

        // host
        $properties->property('host',function($property) {
            $property
                ->title('Host')
                ->type('text')
                ->group('host_group')
                ->required(true)
                ->default('');
        });
        // port
        $properties->property('port',function($property) {
            $property
                ->title('Port')
                ->type('number')       
                ->group('host_group')            
                ->required(true)      
                ->default(25);
        });
        // ssl
        $properties->property('ssl',function($property) {
            $property
                ->title('SSL')
                ->id('ssl')
                ->description('Use ssl encryption')
                ->group('host_group')
                ->type('boolean')              
                ->default(false);
        });

        $properties->property('user',function($property) {
            $property
                ->title('User')
                ->type('group')
                ->displayType('segment')
                ->required(true)              
                ->value('user');           
        });
        // username
        $properties->property('username',function($property) {
            $property
                ->title('Username')
                ->type('text')
                ->group('user')
                ->required(true)    
                ->default('');
        });
        // password
        $properties->property('password',function($property) {
            $property
                ->title('Password')
                ->type('password')
                ->group('user')
                ->required(true)    
                ->default('');
        });
    }
}
