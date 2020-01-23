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
     * @return void
     */
    public function install();

    /**
     * UnInstall extension
     *
     * @return void
     */
    public function unInstall();

    /**
     * Set extension as primary
     *
     * @return void
     */
    public function setPrimary();

    /**
     * Return true if extension is primary
     *
     * @return boolean
     */
    public function isPrimary();
}
