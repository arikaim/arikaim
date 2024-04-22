<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\App;

use Arikaim\Core\Utils\Curl;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\System\Config;

/**
 * Arikaim store
*/
class ArikaimStore 
{       
    const HOST                 = 'http://arikaim.com';
    const SIGNUP_URL           = Self::HOST . '/signup';  
    const LOGIN_API_URL        = '';
    const PACKAGE_VERSION_URL  = Self::HOST . '/api/repository/package/version/';
    const PACKAGE_DOWNLOAD_URL = Self::HOST . '/api/repository/package/download';
    const ORDER_REGISTER_URL   = Self::HOST . '/api/arikaim/order/register';
 
    const ORDER_TYPE_ENVATO   = 'envato';

    /**
     * Data config file name
     *
     * @var string
     */
    protected $configFile;

    /**
     * Config
     *
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     * 
     * @param string $configfileName
     */
    public function __construct(string $configfileName = 'arikaim-store.php')
    {         
        $this->configFile = Path::CONFIG_PATH . $configfileName;
      
        $this->config = new Config($configfileName,Path::CONFIG_PATH);
        if ($this->config->hasConfigFile($configfileName) == false) {
            $this->clear();
            $this->config->save();
        }
    }

    /**
     * Create obj
     *
     * @return Self
     */
    public static function create()
    {
        return new Self();
    }

    /**
     * Get config refernce
     *
     * @return Collection
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get package key
     *
     * @param string|null $repository
     * @return string|null
     */
    public function getPackageKey(?string $repository): ?string
    {
        if (empty($repository) == true) {
            return null;
        }

        $packages = $this->getPackages();
        foreach($packages as $package) {
            if ($package['repository'] == $repository) {
                return $package['key'] ?? null;
            }
        }

        return null;
    }

    /**
     * Return true if cust have account token
     *
     * @return boolean
     */
    public function isLogged(): bool
    {
        return (empty($this->config->getByPath('account/token',null)) == false);
    }

    /**
     * Get orders
     *
     * @return array
     */
    public function getProduct(): array
    {
        $product = $this->config->get('product',[]);

        return (\is_array($product) == false) ? [] : $product;
    }

    /**
     * Get packages
     *
     * @return array
     */
    public function getPackages(): array
    {
        $packages = $this->config->get('packages',[]);

        return (\is_array($packages) == false) ? [] : $packages;
    }

    /**
     * Init data
     *
     * @return void
     */
    public function clear(): void
    {
        $this->config->withData([
            'account'  => [],
            'packages' => [],
            'product'  => []
        ]);
    }

    /**
     * Logout (deletes user token)
     *
     * @return boolean
     */
    public function logout(): bool
    { 
        return true;
    }

    /**
     * Convert config data to array
     *
     * @return array
     */
    protected function toArray(): array
    {
        return $this->config->toArray();
    }

    /**
     * Is curl installed
     *
     * @return boolean
     */
    public function hasCurl(): bool
    {
        return Curl::isInsatlled();
    }

    /**
     * Fetch packages list 
     *
     * @param string $type
     * @param string|null $page
     * @param string $search
     * @return mixed
     */
    public function fetchPackages(string $type, ?string $page = '1', string $search = '')
    {
        $page = (empty($search) == true) ? $page : '/' . $page;
        $url = Self::HOST . '/api/store/product/list/' . $type . '/' . $search . $page;
         
        return Curl::get($url);
    }

    /**
     * Fetch package details 
     *
     * @param string $uuid   
     * @return mixed
     */
    public function fetchPackageDetails(string $uuid)
    {
        $url = $this->getPackageDetailsUrl($uuid);
                
        return Curl::get($url);
    }

    /**
     * Gte package details requets url
     *
     * @param string $uuid
     * @return string
     */
    public function getPackageDetailsUrl(string $uuid): string
    {
        return Self::HOST . '/api/products/product/details/' . $uuid;
    }

    /**
     * Get package version url
     *
     * @param string $packageName
     * @return string
     */
    public function getPackageVersionUrl(string $packageName): string
    {
        return Self::PACKAGE_VERSION_URL . $packageName;        
    }    

    /**
     * Get signup url
     */
    public function getSignupUrl(): string
    {
        return Self::SIGNUP_URL;
    }

    /**
     * Get signup url
    */
    public function getLoginUrl(): string
    {
        return Self::LOGIN_API_URL;
    }
}
