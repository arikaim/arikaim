<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\View;

use Arikaim\Core\Interfaces\View\ComponentInterface;

/**
 * Extension interface
 */
interface HtmlComponentInterface extends ComponentInterface
{  
    /**
     * Init component
     *
     * @return void
     */
    public function init(): void; 

    /**
     * Resolve component
     *
     * @param array $params
     * @return bool
     */
    public function resolve(array $params = []): bool;
}
