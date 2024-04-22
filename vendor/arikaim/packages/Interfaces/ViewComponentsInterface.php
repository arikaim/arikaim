<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Packages\Interfaces;

/**
 * Package view components interface
 */
interface ViewComponentsInterface 
{  
    /**
     * Get package components list
     *
     * @param string|null $parent
     * @param string $type
     * @return array
     */
    public function getComponents(?string $parent = null, string $type = 'components'): array;

    /**
     * Get package emails list
     *
     * @param string|null $path
     * @return array
     */
    public function getEmails(?string $parent = null): array;

    /**
     * Get package pages list
     *
     * @param string|null $path
     * @return array
     */
    public function getPages(?string $parent = null): array;
}
