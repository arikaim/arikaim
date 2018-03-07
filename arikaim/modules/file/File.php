<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2018 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/
namespace Arikaim\Modules\File;

use League\Flysystem\MountManager;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

use Arikaim\Core\FileSystem\File as FileUtils;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Module\Module;

class File extends Module
{
    private $manager;

    public function __construct()
    {
        $this->manager = new MountManager();
        $local_adapter = new Local(FileUtils::getFilesPath());
        $this->mount('local',$local_adapter);

        // module details
        $this->setServiceName('file');
        $this->setModuleVersion('1.0');        
        $this->setBootable();
    }

    public function mount($name,$adapter)
    {
        $filesystem = new Filesystem($adapter);
        return $this->manager->mountFilesystem($name,$filesystem);
    }

    public function __call($method, $args) 
    {
        return Utils::call($this->manager,$method,$args);       
    }

    public function get($name)
    {
        return $this->manager->getFilesystem($name);
    }
}
