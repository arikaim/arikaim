<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Recaptcha;

use Arikaim\Core\Arikaim;
use Arikaim\Core\Driver\Traits\Driver;
use Arikaim\Core\Interfaces\Driver\DriverInterface;
use Arikaim\Extensions\Captcha\Interfaces\CaptchaInterface;

/**
 * Recaptcha driver class
 */
class RecaptchaDriver implements DriverInterface,CaptchaInterface
{   
    use Driver;

    /**
     * Verification errors 
     *
     * @var array
     */
    private $verifyErrors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDriverParams('recaptcha','captcha','ReCaptcha','Driver for Goolge ReCaptcha service');
        $this->verifyErrors = [];
    }

    /**
     * Initialize driver
     *
     * @return void
     */
    public function initDriver($properties)
    {
        $this->instance = new \ReCaptcha\ReCaptcha($properties->getValue('secret_key'));       
        if (empty($properties->getValue('expected_hostname')) == false) {
            $this->instance->setExpectedHostname($properties->getValue('expected_hostname'));
        }        
    }

    /**
     * Create driver config properties array
     *
     * @param Arikaim\Core\Collection\Properties $properties
     * @return array
     */
    public function createDriverConfig($properties)
    {
        $properties->property('site_key',function($property) {
            $property
                ->title('Site Key')
                ->type('text')
                ->default('6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI')
                ->required(true);
        });

        $properties->property('secret_key',function($property) {
            $property
                ->title('Secret Key')
                ->type('text')
                ->default('6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe')
                ->required(true);
        });

        $properties->property('expected_hostname',function($property) {
            $property
                ->title('Expected Hostname')
                ->type('url')
                ->default('');
        });
    }

    /**
     * Verify captcha
     *
     * @param mixed $captchaResponse
     * @param mixed $remoteIp
     * @return bool
     */
    public function verify($captchaResponse, $remoteIp)
    {
        if (is_object($this->instance) == false) {
            return false;
        }
        $this->verifyErrors = [];
        $response = $this->instance->verify($captchaResponse,$remoteIp);
        if ($response->isSuccess() == true) {
            return true;
        }
        $this->verifyErrors = $response->getErrorCodes();
        
        return false;
    }

    /**
     * Get verification errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->verifyErrors;
    }   
}
