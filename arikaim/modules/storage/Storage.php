<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2018 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/
namespace Arikaim\Modules\Storage;

use League\Flysystem\MountManager;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

use Arikaim\Core\FileSystem\File;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Module\Module;

class Storage extends Module
{
    private $manager;

    public function __construct()
    {
        $this->manager = new MountManager();
        // module details
        $this->setServiceName('storage');
        $this->setVersion('1.0');        
        $this->setBootable();
    }

    public function boot()
    {
        $local_adapter = new Local(File::getStoragePath());
        $this->mount('local',$local_adapter);
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
