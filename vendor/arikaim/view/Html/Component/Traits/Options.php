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
 * Component options
 */
trait Options
{
    /**
     * Component styles file content
     *
     * @var array
     */
    protected $styles = [];

    /**
     * Component data file content
     *
     * @var array
     */
    protected $data = [];

    /**
     * Remove include options
     *
     * @var boolean
     */
    protected $removeIncludeOptions = false;

    /**
     * Optins file
     *
     * @var string
     */
    protected $optionsFile = 'component.json';

    /**
     * Component type option
     *
     * @return void
     */
    protected function componentTypeOption(): void
    { 
        // component type option
        $componentType = $this->getOption('component-type');
        if (empty($componentType) == false) {
            $this->setComponentType($componentType);
        } 
    }

    /**
     * Process component include styles option
     *
     * @return void
     */
    protected function processStylesOption(): void
    {
        // component "include-styles" option
        if ($this->getOption('include-styles',false) == true) {
            $this->styles = $this->loadJsonFile('styles.json','styles');
        } 
    }

    /**
     * Process component include data file option
     *
     * @return void
     */
    protected function processDataOption(): void
    {
        // component "include-data" option
        if ($this->getOption('include-data',false) == true) {          
            $this->data = $this->loadJsonFile('data.json','data');
        } 
    }

    /**
     * Load styles json file
     *
     * @return bool
     */
    protected function loadJsonFile(string $fileName, string $key): array
    {       
        $this->files[$key]['file_name'] = $this->fullPath . $fileName;
    
        return (\is_file($this->files[$key]['file_name']) == true) ?
            \json_decode(\file_get_contents($this->files[$key]['file_name']),true) : [];                             
    }

    /**
     * Add styles to context array
     *
     * @return void
     */
    public function mergeStyles(): void
    {
        $this->context['styles'] = \array_replace($this->styles,$this->context['styles'] ?? []);
    }

    /**
     * Add data to context array
     *
     * @return void
     */
    public function mergeData(): void
    {
        $this->context['data'] = \array_replace($this->data,$this->context['data'] ?? []);
    }

    /**
     * Set option file name
     *
     * @param string $name  
     * @return void
     */
    public function setOptionFile(string $name): void
    {
        $this->optionsFile = $name;
    }

    /**
     * Get option
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;       
    }

    /**
     * Set option value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setOption(string $key, $value): void
    {
        $this->options[$key] = $value;
    }

    /**
     * Load options json file
     *
     * @param bool $useParent
     * @return void
     */
    public function loadOptions(bool $useParent = true): void
    {         
        $this->resolveOptionsFileName(null,$useParent);
        $optionsFile = $this->getOptionsFileName();

        if (empty($optionsFile) == true) {
            return;
        }

        $options = \json_decode(\file_get_contents($optionsFile),true);
               
        if (($this->removeIncludeOptions == true) && (isset($options['include']) == true)) {
            unset($options['include']);
        }

        $this->options = $options;    
    }

    /**
     * Get options file name
     *
     * @return string|null
     */
    protected function getOptionsFileName(): ?string
    {
        return $this->files['options']['file_name'] ?? null;         
    }

    /**
     * Set options file name
     *
     * @param string $fileName
     * @return void
     */
    protected function setOptionsFileName(string $fileName): void
    {
        $this->files['options']['file_name'] = $fileName;
    }

    /**
     * Resolve options file name
     *
     * @param string|null $path  
     * @param bool $useParent
     * @return void
     */
    protected function resolveOptionsFileName(?string $path = null, bool $useParent = true): void
    {   
        $path = $path ?? $this->fullPath;
        $fileName = $path . $this->optionsFile;

        if (\is_file($fileName) == true) {
            $this->removeIncludeOptions = false;
            $this->setOptionsFileName($fileName);
            return;
        }

        if ($useParent == false) {
            // skip parent folder
            return;   
        }

        // Check for parent component options file             
        $fileName = $this->getRootPath() . $this->optionsFile;

        if (\is_file($fileName) == true) {
            // disable includes from parent component     
            $this->removeIncludeOptions = true;
            $this->setOptionsFileName($fileName);
        }        
    }    
}
