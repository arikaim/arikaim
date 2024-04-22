<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Controllers\Traits;

/**
 * Captcha trait
*/
trait Captcha 
{        
    /**
     * Captcha errors 
     *
     * @var array|null
     */
    protected $captchaErrors = null;

    /**
     * Verify captcha
     *   
     * @param \Psr\Http\Message\ServerRequestInterface $request    
     * @param Validator|array|null $data
     * @return boolean
    */
    public function verifyCaptcha($request, $data): bool
    {
        $data = (\is_object($data) == true) ? $data->toArray() : $data;

        $driverName = $this->get('options')->get('captcha.current');
        $driver = $this->get('driver')->create($driverName);
        $this->captchaErrors = null;
        $result = $driver->verify($request,$data);

        if ($result == false) {
            $this->captchaErrors = $driver->getErrors();
            $this->error('errors.captcha'); 
            return false;     
        }   

        return true;
    }

    /**
     * Clear captcha errors
     *
     * @return void
     */
    public function clearCaptchaErrors(): void 
    {
        $this->captchaErrors = null;
    }

    /**
     * Get captcha errors
     *
     * @return array|null
     */
    public function getCaptchaErrors(): ?array
    {
        return $this->captchaErrors;
    } 
}
