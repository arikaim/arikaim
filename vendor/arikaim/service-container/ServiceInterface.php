<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Service;

/**
 * Service interface
 */
interface ServiceInterface 
{  
    /**
     * Get include service list 
     *   
     * @return array|null
     */
    public function getIncludeServices(): ?array;

    /**
     * Get service name
     *
     * @return string
     */
    public function getServiceName(): string;

    /**
     * Set service name
     *
     * @param string $title
     * @return void
     */
    public function setServiceName(string $name): void;

    /**
     * Get service title
     *
     * @return string|null
     */
    public function getServiceTitle(): ?string;

    /**
     * Set service title
     *
     * @param string $title
     * @return void
     */
    public function setServiceTitle(string $title): void;

    /**
     * Get service description
     *
     * @return string|null
     */
    public function getServiceDescription(): ?string;

    /**
     * Set service description
     *
     * @param string $description
     * @return void
     */
    public function setServiceDescription(string $description): void;
}
