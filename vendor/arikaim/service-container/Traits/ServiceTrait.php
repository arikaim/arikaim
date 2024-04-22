<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Service\Traits;

/**
 *  Service trait
 */
trait ServiceTrait
{    
    /**
     * Service name
     *
     * @var string
     */
    protected $serviceName;

    /**
     * Service title
     *
     * @var string|null
     */
    protected $serviceTitle;

    /**
     * Service description
     *
     * @var string|null
     */
    protected $serviceDescription;

    /**
     * Services list
     *
     * @var array|null
     */
    protected $services = null;

    /**
     * Set include service list 
     *
     * @param array $services
     * @return void
     */
    public function includeServices(array $services): void
    {
        $this->services = $services;
    }

    /**
     * Get include service list 
     *   
     * @return array|null
     */
    public function getIncludeServices(): ?array
    {
        return $this->services;
    }
    
    /**
     * Get service name
     *
     * @return string
     */
    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    /**
     * Set service name
     *
     * @param string $name
     * @return void
     */
    public function setServiceName(string $name): void
    {
        $this->serviceName = $name;
    }

    /**
     * Get service title
     *
     * @return string|null
     */
    public function getServiceTitle(): ?string
    {
        return $this->serviceTitle;
    }

    /**
     * Set service title
     *
     * @param string $title
     * @return void
     */
    public function setServiceTitle(string $title): void
    {
        $this->serviceTitle = $title;
    }

    /**
     * Get service description
     *
     * @return string|null
     */
    public function getServiceDescription(): ?string
    {
        return $this->serviceDescription;
    }

    /**
     * Set service description
     *
     * @param string $description
     * @return void
     */
    public function setServiceDescription(string $description): void
    {
        $this->serviceDescription = $description;
    }
}
