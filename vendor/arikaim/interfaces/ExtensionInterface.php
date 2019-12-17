<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces;

/**
 * Extension interface
 */
interface ExtensionInterface 
{  
    /**
     * Install extension callback
     *
     * @return mixed
     */
    public function install();

    /**
     * UnInstall extension
     *
     * @return boolean
     */
    public function unInstall();
}
