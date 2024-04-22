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
 * Component editor options
 */
trait ComponentEditor
{
    /**
     * Optins file
     *
     * @var string
     */
    protected $editorOptionsFile = 'editor.json';

    /**
     * Editor options
     *
     * @var array
     */
    protected $editorOptions = [];
    
    /**
     * Add editor options to context array
     *
     * @return void
     */
    public function mergeEditorOptions(): void
    {
        $this->context['_editor'] = \array_replace($this->editorOptions,$this->context['_editor'] ?? []);
    }

    /**
     * Get editor option
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getEditorOption(string $key, $default = null)
    {
        return $this->editorOptions[$key] ?? $default;       
    }

    /**
     * Set editor option value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setEditorOption(string $key, $value): void
    {
        $this->editorOptions[$key] = $value;
    }

    /**
     * Load editor options json file
     *
     * @return void
     */
    public function loadEditorOptions(): void
    {         
        $this->resolveEditorOptionsFileName();
        $optionsFile = $this->getEditorOptionsFileName();
        $this->editorOptions = [];

        if (empty($optionsFile) == false) {
            $this->editorOptions = \json_decode(\file_get_contents($optionsFile),true);
            $this->mergeEditorOptions();
        }
    }

    /**
     * Get editor options file name
     *
     * @return string|null
     */
    protected function getEditorOptionsFileName(): ?string
    {
        return $this->files['editor']['file_name'] ?? null;         
    }

    /**
     * Set editor options file name
     *
     * @param string $fileName
     * @return void
     */
    protected function setEditorOptionsFileName(string $fileName): void
    {
        $this->files['editor']['file_name'] = $fileName;
    }

    /**
     * Resolve editor options file name
     *
     * @param string|null $path  
     * @return void
     */
    protected function resolveEditorOptionsFileName(?string $path = null): void
    {           
        $fileName = ($path ?? $this->fullPath) . $this->editorOptionsFile;

        if (\is_file($fileName) == true) {
            $this->setEditorOptionsFileName($fileName);
            return;
        }              
    }    
}
