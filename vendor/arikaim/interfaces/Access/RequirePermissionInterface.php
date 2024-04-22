<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\Access;

/**
 * Require Permission interface
 */
interface RequirePermissionInterface
{    
    /**
     * Sould return required permission
     *
     * @return string
     */
    public function getRequiredPermission();
}
