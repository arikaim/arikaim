<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Image;

use Intervention\Image\ImageManager;
use Arikaim\Core\Extension\Module;

/**
 * Image class
 */
class Image extends Module
{
    const IMAGICK_DRIVER = 'imagick';
    const GD_DRIVER      = 'gd';

    /**
     * Image menagaer class
     *
     * @var ImageManager
     */
    private $manager;

    /**
     * Install module
     *
     * @return void
     */
    public function install()
    {
        $this->registerService('Image');
    }

    /**
     * Boot
     */
    public function boot()
    {
        // create image manager  
        $this->manager = $this->createImageManager();
    }

    /**
     * Create image manager instance
     *
     * @param string $driver
     * @return ImageManager|null
     */
    public function createImageManager($driver = Self::GD_DRIVER) 
    {
        return new ImageManager(['driver' => $driver]);
    } 

    /**
     * Get drivers list
     *
     * @return array
     */
    public function getDrivers()
    {
        $result = [];
        if (\extension_loaded('gd') == true) {
            $result[] = Self::GD_DRIVER;
        }
        if (\extension_loaded('imagick') == true) {
            $result[] = Self::IMAGICK_DRIVER;
        }

        return $result;
    }

    /**
     * Get ImageManager instance
     *
     * @return ImageManager
     */
    public function getInstance()
    {
        return $this->manager;
    }

    /**
     * Test module
     *
     * @return boolean
     */
    public function test()
    {
        $image_data = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVQI12NgaAQAAIQAghhgRykAAAAASUVORK5CYII=";
        try {
            $image = $this->manager->make($image_data);
        } catch(\Exception $e) {
            $this->error = $e->getMessage();         
            return false;
        }

        return true;
    }
}
