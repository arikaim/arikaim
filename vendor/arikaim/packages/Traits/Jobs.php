<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Packages\Traits;

use Arikaim\Core\Utils\Factory;
use DirectoryIterator;

/**
 * Package jobs trait
*/
trait Jobs 
{
    /**
     * Get extension jobs path
     *   
     * @return string
    */
    public function getJobsPath(): string   
    {
        return $this->path . $this->getName() . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR;
    }

     /**
     * Get extension jobs
     *
     * @return array
     */
    public function getPackageJobs(): array
    {
        $path = $this->getJobsPath();
        if (\file_exists($path) == false) {
            return [];
        }

        $result = [];
        foreach (new DirectoryIterator($path) as $file) {
            if (
                $file->isDot() == true || 
                $file->isDir() == true ||
                $file->getExtension() != 'php'
            ) continue;
          
            $item['base_class'] = \str_replace('.php','',$file->getFilename());
            $job = Factory::createJob($item['base_class'],$this->getName());
            if ($job != null) {
                $item['class'] = \get_class($job);
                if ($job != null) {
                    $item['name'] = $job->getName();
                    $result[] = $item;
                }
            }
        }

        return $result;
    }
}
