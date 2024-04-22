<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Cors;

use Arikaim\Modules\Cors\CorsMiddleware;
use Arikaim\Core\Extension\Module;

/**
 * Cors middleware module class
 */
class Cors extends Module 
{
    /**
     * Boot module
     *
     * @return void
     */
    public function boot()
    {
        $this->addMiddlewareClass(CorsMiddleware::class);
    }
}
