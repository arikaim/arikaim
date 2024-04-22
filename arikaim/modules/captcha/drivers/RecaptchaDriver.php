<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Captcha\Drivers;

use Arikaim\Core\Arikaim;
use Arikaim\Core\Driver\Traits\Driver;
use Arikaim\Core\Interfaces\Driver\DriverInterface;
use Arikaim\Modules\Captcha\CaptchaInterface;

/**
 * Recaptcha driver class
 */
class RecaptchaDriver implements DriverInterface, CaptchaInterface
{   
    use Driver;

    /**
     * Verification errors 
     *
     * @var array|null
     */
    private $errors = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDriverParams('recaptcha','captcha','ReCaptcha','Driver for Goolge ReCaptcha service');      
    }

    /**
     * Initialize driver
     *
     * @return void
     */
    public function initDriver($properties)
    {
        $secretKey = \trim($properties->getValue('secret_key',''));
        $hostName = \trim($properties->getValue('expected_hostname',''));

        $this->instance = new \ReCaptcha\ReCaptcha($secretKey);       
        if (empty($properties->getValue('expected_hostname')) == false) {
            $this->instance->setExpectedHostname($hostName);
        }        
        
        $this->clearErrors();
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
     * @param \Psr\Http\Message\ServerRequestInterface $request    
     * @param array|null $data
     * @return bool
     */
    public function verify($request, ?array $data = null): bool
    {
        if (\is_object($this->instance) == false) {
            return false;
        }
        $this->clearErrors();
        
        $captchaResponse = $data['g-recaptcha-response'] ?? null;
        if (empty($captchaResponse) == true) {
            return false;
        }
        $remoteIp = $request->getAttribute('client_ip');

        $response = $this->instance->verify($captchaResponse,$remoteIp);
        if ($response->isSuccess() == true) {
            return true;
        }

        $this->errors = $response->getErrorCodes();
        
        return false;
    }

    /**
     * Get verification errors
     *
     * @return array|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }   

    /**
     * Clear errors
     *
     * @return void
     */
    public function clearErrors(): void
    {
        $this->errors = null;
    }
}
