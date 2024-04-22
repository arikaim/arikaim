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
 * Cache interface
 */
interface ImportModelInterface
{ 
    /**
     * Return list with skipped column names which are not included in import
     *
     * @return array
     */
    public function getSkipedImportColumns(): array;
}
