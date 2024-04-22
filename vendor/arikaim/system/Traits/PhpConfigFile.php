<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\System\Traits;

use Arikaim\Core\Utils\File;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Utils\Path;

/**
 * Php Config file loader and writer
 */
trait PhpConfigFile
{
    /**
     * Config array comments
     *
     * @var array
    */
    protected $comments = [];

    /**
     * Include file
     *  
     * @param string $fileName  Full file name
     * @return array|null
     */
    public function include(string $fileName): ?array 
    {       
        return (\file_exists($fileName) == true) ? include($fileName) : null;             
    }   

    /**
     * Include config file
     *
     * @param string $fileName
     * @param string|null $extensionName
     * @return array|null
     */
    public function includeConfigFile(string $fileName, ?string $extensionName = null): ?array
    {
        $configFile = empty($extensionName) ? Path::CONFIG_PATH . $fileName : Path::getExtensionConfigPath($extensionName) . $fileName;

        return (\file_exists($configFile) == true) ? include($configFile) : null;
    }

    /**
     * Include php array
     *
     * @param string $fileName
     * @return array|null
     */
    public function includePhpArray(string $fileName): ?array
    {
        if (\file_exists($fileName) == false) {
            return null;
        }
        $code = \file_get_contents($fileName);
        $result = eval('?>' . $code);

        return (\is_array($result) == false) ? null : $result;
    } 

    /**
     * Set array key comment
     *
     * @param string $comment
     * @param string $key
     * @return void
     */
    protected function setComment(string $comment, string $key): void
    {
        $this->comments[$key] = $comment;
    }

    /**
     * Get array imtem comment as text
     *
     * @param string $key
     * @return string
     */
    protected function getCommentsText(string $key): string
    {
        return (isset($this->comments[$key]) == true) ? "\t// " . $this->comments[$key] . "\n" : '';
    }

    /**
     * Return config file content
     *
     * @param array $data
     * @return string
     */
    private function getFileContent(array $data): string 
    {   
        $code = $this->getFileContentHeader();
        $code .= $this->exportConfig($data);

        return $code;
    }

    /**
     * Export array as text
     *
     * @param array $data
     * @param string $arrayKey
     * @param int $currentTab
     * @return string
     */
    protected function exportArray(array $data, string $arrayKey, int $currentTab = 1): string
    {     
        $content = '';  
        $maxTabs = $this->determineMaxTabs($data);
        $currentTabs = $this->getTabs($currentTab);
   
        foreach ($data as $key => $value) {
            $content .= (empty($content) == false) ? ",\n" : '';
          
            if (\is_array($value) == true) {                         
                $content .= $this->exportArray($value,$key,$currentTab + 1);              
            } else {
                $content .= $this->exportItem($key,$value,$maxTabs,$currentTab + 1);               
            }
        }
        $comment = $this->getCommentsText($arrayKey);
        $keyText = (\is_numeric($arrayKey) == true) ? '' : "'" . $arrayKey . "' => ";

        return "$comment" . $currentTabs . $keyText . "[\n" . $content . "\n"  . $currentTabs . "]";
    }

    /**
     * Export item as text
     *
     * @param string|int $key
     * @param mixed $value
     * @return string
     */
    protected function exportItem($key, $value, int $maxTabs, int $startTab = 1): string
    {
        $tabs = $maxTabs - $this->determineTabs($key);
        $value = Utils::getValueAsText($value);
        $startTabs = $this->getTabs($startTab);

        if (\is_numeric($key) == true) {
            return $this->getTabs($tabs) . "$value";
        } else {
            return $startTabs . "'$key'" . $this->getTabs($tabs) . "=> $value";
        }
    }

    /**
     * Export config as text
     *
     * @param array $data
     * @return string
     */
    protected function exportConfig(array $data): string
    {
        $content = '';
        $maxTabs = $this->determineMaxTabs($data);

        foreach ($data as $key => $item) {
            $content .= (empty($content) == false) ? ",\n" : '';
            if (\is_array($item) == true) {
                $content .= $this->exportArray($item,$key,1);
            } else {                              
                $content .= $this->exportItem($key,$item,$maxTabs);
            }
        }

        return "return [\n $content \n];\n";      
    }

    /**
     * Get config file header
     *
     * @return string
     */
    private function getFileContentHeader(): string 
    {
        $code = "<?php \n/**\n";
        $code .= "* Arikaim\n";
        $code .= "* @link        http://www.arikaim.com\n";
        $code .= "* @copyright   Copyright (c) 2017-" . date('Y') . " <info@arikaim.com>\n";
        $code .= "* @license     http://www.arikaim.com/license\n";
        $code .= "*/\n\n";

        return $code;
    }

    /**
     * Get max tabs count
     *
     * @param array $data
     * @param integer $tabSize
     * @return integer
     */
    private function determineMaxTabs(array $data, int $tabSize = 4): int
    {
        $keys = [];
        foreach ($data as $key => $value) {
            $keys[] = \strlen($key);
        }
        $len = (\count($keys) == 0) ? 1 : \max($keys);

        return \ceil($len / $tabSize);
    }

    /**
     * Get tabs count for array key
     *
     * @param string $key
     * @param integer $tabSize
     * @return integer
     */
    private function determineTabs(string $key, int $tabSize = 4): int
    {
        return \round(\strlen($key) / $tabSize);
    }

    /**
     * Get tabs text
     *
     * @param integer $count
     * @return string
     */
    private function getTabs(int $count): string
    {       
        return ($count <= 0) ? ' ' : \str_repeat("\t",$count);  
    }

    /**
     * Save config file
     *
     * @param string $fileName
     * @param array $data
     * @return bool
    */
    public function saveConfigFile(string $fileName, array $data): bool
    {
        if (File::isWritable($fileName) == false) {
            File::setWritable($fileName);
        }
        $content = $this->getFileContent($data);  
     
        return (bool)File::write($fileName,$content);       
    }
}
