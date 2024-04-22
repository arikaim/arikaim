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

/**
 * Include options for view components
 */
trait IncludeOption
{
    /**
     * Process component include js files option
     *      
     * @return void
     */
    protected function processIncludeOption(): void
    { 
        $include = $this->getOption('include',[]);   
        $include = $include['js'] ?? null;
        if (empty($include) == true) {
            return;
        }

        // include component files
        foreach ($include as $item) {             
            $this->files['js'][] = $this->resolveIncludeFile($item);
        }                 
    }

    /**
     * Resolve include file
     *
     * @param string $includeFile  Component or Url     
     * @return array
     */
    protected function resolveIncludeFile(string $includeFile): array
    {
        if (\filter_var($includeFile,FILTER_VALIDATE_URL) !== false) {             
            $tokens = \explode('|',$includeFile);
            $tokens[0] = 'external';
        
            return [
                'url'              => $tokens[0],
                'params'           => (isset($tokens[1]) == true) ? $tokens : [],
                'source_component' => 'url'
            ];
        } 
        
        $component = $this->create($includeFile,'en');
        $component->init();
        $files = $component->getIncludeFile('js');
     
        $this->addIncludedComponent($includeFile,$this->componentType,$component->id);
        
        return [
            'url'            => $files,
            'component_name' => $includeFile,
            'component_id'   => $component->id,
            'component_type' => $component->getComponentType()
        ];                     
    }
}
