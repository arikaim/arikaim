<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Collection\Traits;

/**
 * Properties descriptor
*/
trait Descriptor 
{  
    /**
     * Descriptor instance
     *
     * @var object|null
     */
    protected $descriptor = null;

    /**
     * Descriptor class
     *
     * @var null|string
     */
    protected $descriptorClass = null;

    /**
     * Set descriptor class
     *
     * @param string $class
     * @return void
     */
    public function setDescriptorClass(string $class): void
    {
        $this->descriptorClass = $class;
    }

    /**
     * Get descriptor
     *
     * @return object|null
     */
    public function descriptor(): ?object
    {
        if (($this->descriptor == null) && (empty($this->descriptorClass) == false)) {
            $this->descriptor = new $this->descriptorClass();
            $this->initDescriptor();
        }
        
        return $this->descriptor;
    }

    /**
     * Init descriptor properties 
     *
     * @return void
     */
    protected function initDescriptor(): void
    {
    }
}
