<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Image\Service;

use Intervention\Image\ImageManager;

use Arikaim\Core\System\Error\Traits\TaskErrors;
use Arikaim\Core\Utils\File;

use Arikaim\Modules\Image\Classes\GdImageFilter;
use Arikaim\Core\Service\Service;
use Arikaim\Core\Service\ServiceInterface;
use Exception;
use Closure;

/**
 * Image service class
*/
class Image extends Service implements ServiceInterface
{
    use TaskErrors;

    /**
     * Image menagaer class
     *
     * @var ImageManager
     */
    private $manager;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setServiceName('image');
        $this->manager = new ImageManager(['driver' => 'gd']);
    }

    /**
     * Get image manager
     *
     * @return ImageManager
     */
    public function manager() 
    {
        return $this->manager;
    }

    /**
     * Oacity filter
     *
     * @param \GdImage|resource $image
     * @param float $opacity
     * @return \GdImage|resource
     */
    public function opacity($image, float $opacity = 0.5)
    {
        return GdImageFilter::opacity($image,$opacity);
    }

    /**
     * Create image
     *
     * @param mixed $source
     * @return object|null
     */
    public function make($source)
    {
        $this->clearErrors();
        try {
            $image = $this->manager->make($source);
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            return null;
        }

        return $image;
    }

    /**
     * Save image to file
     *
     * @param Image $source
     * @param string $path
     * @param string $fileName
     * @return boolean
     */
    public function save($image, string $path, string $fileName): bool
    {
        if (File::exists($path) == false) {
            throw new Exception('Destination folder not exist');
            return false;
        }
        if (File::isWritable($path) == false) {
            File::setWritable($path);
        }
        if (File::isWritable($path) == false) {
            throw new Exception('Destination folder not writable!');
            return false;
        }
        
        return (bool)$image->save($path . $fileName);
    } 

    /**
     * Resize image
     *
     * @param mixed $source
     * @param integer $width
     * @param integer $height
     * @param Closure|null $callback
     * @return mixed
     */
    public function resize($source, int $width, int $height, ?Closure $callback = null)
    {
        $image = $this->make($source);
        if (\is_null($image) == true) {
            return null;
        }
        $image->resize($width,$height);
        if (empty($callback) == false) {
            $process = function($image,$callback) {
                $callback($image);
                return $image;
            };
            $image = $process($image,$callback);
        }

        return $image;
    }

    /**
     * Get image size
     *
     * @param mixed $source
     * @return array|null
     */
    public function getSize($source): ?array
    {       
        $image = $this->make($source);
        if (\is_null($image) == true) {
            return null;
        }
       
        return [
            'width'  => $image->width(),
            'height' => $image->height()
        ];
    }
}
