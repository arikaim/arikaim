<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits\Schema;

use Arikaim\Core\Utils\DateTime;

/**
 * Import model schema options
 * 
 * For skipping columns in import in schema class add var
 *  protected $importSkipColumns = [] - list with skiped column keys
 */
trait Import
{    
    /**
     *  Return list with skipped column names which are not included in import
     *
     * @return string
     */
    public function getSkipedImportColumns(): array
    {
        return $this->importSkipColumns ?? [];
    }
}
