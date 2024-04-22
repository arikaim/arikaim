<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     View
 */
namespace Arikaim\Core\View\Html\Component\Traits;

use Arikaim\Core\Interfaces\View\ComponentDataInterface;

/**
 * Data source for components
 */
trait Data
{
    /**
     * Component data file
     *
     * @var string|null
     */
    protected $dataFile = null;

    /**
     * Get component data file.
     * 
     * @return string|null
     */
    public function getDataFile(): ?string
    {
        return $this->dataFile;
    }

    /**
     * Resolve component data file
     *
     * @return void
     */
    protected function resolveDataFile(): void
    {
        $fileName = $this->fullPath . $this->name . '.php';
       
        $this->dataFile = (\is_file($fileName) == true) ? $fileName : null;       
    }

    /**
     * Process daat file
     *
     * @param array $params
     * @param Container|null $container
     * @return bool
     */
    protected function processDataFile(array $params, $container = null): bool
    {
        $this->resolveDataFile();

        if (empty($this->dataFile) == false) {
            // include data file
            $componentData = require_once($this->dataFile);                       
            if ($componentData instanceof ComponentDataInterface) {                   
                $data = $componentData->getData($params,$container);              
                $this->mergeContext($data); 

                return true;              
            }          
        }

        return false;
    }
}
