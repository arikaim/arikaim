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

use Arikaim\Container\Container;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\System\Traits\PhpConfigFile;
use Arikaim\Core\Service\ServiceInterface;
use Exception;
use Closure;

/**
 *  Service container
 */
class ServiceContainer
{
    use PhpConfigFile;

    /**
     *  Default providers config file name
     */
    const CONFIG_FILE_NAME = Path::CONFIG_PATH . 'service-providers.php';

    /**
     * Service container
     *
     * @var Container
    */
    protected $container;

    /**
     * Service providers
     *
     * @var array|null
     */
    protected $serviceProviders = null;

    /**
     * Service providers config file
     *
     * @var string
     */
    private $configFileName;

    /**
     * Constructor
     * 
     * @param string|null $configFileName
     */
    public function __construct(?string $configFileName = null)
    {
        $this->container = new Container();
        $this->configFileName = $configFileName ?? Self::CONFIG_FILE_NAME;   
        $this->load();     
    }

    /**
     * Load service providers
     *
     * @param boolean $reload
     * @return void
     */
    public function load(bool $reload = false): void
    {
        if (($this->serviceProviders === null) || ($reload == true)) {
            $this->serviceProviders = $this->include($this->configFileName);
        }
    }

    /**
     * Get service providers
     *
     * @return array
     */
    public function getProviders(): array
    {
        return $this->serviceProviders ?? [];
    }

    /**
     * Get provider class
     *
     * @param string $name
     * @return array|null
     */
    public function getProvider(string $name): ?array
    {
        return $this->serviceProviders[$name] ?? null;
    }

    /**
     * Check if provider exists
     *
     * @param string $name
     * @return boolean
     */
    public function hasProvider(string $name): bool
    {
        $provider = $this->serviceProviders[$name] ?? null;

        return !empty($provider);
    } 

    /**
     * Get service instance
     *
     * @param string $name
     * @return mixed|null
     */
    public function get(string $name)
    {             
        // check container
        if ($this->container->has($name) == false) {
            $this->bindProvider($name);
        }

        return $this->container->get($name);
    }

    /**
     * Include service
     *
     * @param string $name
     * @param array|null $provider
     * @return boolean
     */
    protected function bindProvider(string $name, ?array $provider = null): bool
    { 
        if ($provider === null) {
            // check prividers
            $provider = $this->getProvider($name);
            if (empty($provider) == true) {
                return false;
            }
        }

        // add in container
        $includeServices = null;
        if (\is_array($provider['include']) == true) {
            $this->bindProviders($provider['include']);
            $includeServices = $this->container->clone($provider['include']);
        }
        $this->container[$name] = function() use($provider,$includeServices) {
            return new $provider['handler']($includeServices);
        };   

        return true;
    }

    /**
     * Bind providers
     *
     * @param array $providersList
     * @return void
     */
    protected function bindProviders(array $providersList): void
    {
        foreach($providersList as $item) {
            $this->bindProvider($item);
        }
    }

    /**
     * Return true if service exists in container
     *
     * @param string $name
     * @return boolean
     */
    public function has(string $name): bool
    {      
        if ($this->container->has($name) == false) {
            $this->bindProvider($name);
        }

        return (bool)$this->container->has($name);
    }

    /**
     * Run closure with serice
     *
     * @param string $name
     * @param Closure $callback
     * @return mixed
     */
    public function with(string $name, Closure $callback)
    {
        if ($this->has($name) == false) {
            return false;
        }
        $service = $this->get($name);

        return $callback($service);
    } 

    /**
     * Register service provider
     *
     * @param string|array $details
     * @return boolean
     */
    public function register($details): bool
    {
        if (\is_string($details) == true) {
            if (\class_exists($details) == false) {         
                return false;
            }
            $provider = new $details();
            if (($provider instanceof ServiceInterface) == false) {
                throw new Exception('Service provider ' . $details . ' not valid service class.');
                return false;
            }
            $provider->boot();
            $details = $this->resolveServiceDetails($provider);
        } else {          
            $details = $this->resolveServiceDetails($details);
        }
       
        // load current array
        $this->serviceProviders = $this->includePhpArray($this->configFileName);
        
        // add item
        $this->serviceProviders[$details['name']] = $details;

        return $this->saveConfigFile($this->configFileName,$this->serviceProviders);       
    }

    /**
     * UnRegister service
     *
     * @param string $name  Name or provider class
     * @return boolean
     */
    public function unRegister(string $name): bool
    {
        if ($this->hasProvider($name) == false) {
            // find provider
            $name = \array_search($name,$this->getProviders());
            if ($name === false) {
                return true;
            }            
        }

        unset($this->serviceProviders[$name]);

        return $this->saveConfigFile($this->configFileName,$this->serviceProviders);     
    }

    /**
     * Resolve service edetails
     *
     * @param ServiceInterface|array $details
     * @throws Exception
     * @return array
     */
    protected function resolveServiceDetails($details): array
    {
        if ($details instanceof ServiceInterface) {
            return [
                'handler' => \get_class($details),
                'name'    => $details->getServiceName(),
                'title'   => $details->getServiceTitle(),
                'include' => $details->getIncludeServices()
            ];
        }

        if (isset($details['name']) == false || isset($details['handler'])) {
            throw new Exception('Service name or handler not valid.');
        }
        $details['title'] = $details['title'] ?? null;
        $details['include'] = $details['include'] ?? null;

        return $details;
    }
}
