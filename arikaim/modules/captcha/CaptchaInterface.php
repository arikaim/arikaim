<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Captcha;

/**
 * Captcha interface
 */
interface CaptchaInterface
{    
    /**
     * Verify captcha
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request    
     * @param array|null $data
     * @return bool
     */
    public function verify($request, ?array $data = null): bool;

    /**
     * Get verification errors
     *
     * @return array|null
     */
    public function getErrors(): ?array;
}
