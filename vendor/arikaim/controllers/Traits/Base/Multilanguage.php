<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Controllers\Traits\Base;

use Arikaim\Core\Http\Cookie;
use Arikaim\Core\Http\Session;
use Arikaim\Core\View\ComponentFactory;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\Collection\Arrays;

/**
 * Multilanguage trait
*/
trait Multilanguage 
{     
    /**
     * Response messages
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Messages component name
     *
     * @var string
     */
    protected $messagesComponentName = '';

    /**
     * Messages loaded
     *
     * @var boolean
     */
    protected $messagesLoaded = false;

    /**
     * Add message to response, first find in messages array if not found display name value as message 
     *
     * @param string $name  
     * @param string|null $default
     * @return ApiController
     */
    public function message(string $name, ?string $default = null)
    {
        $this->result['result']['message'] = $this->getMessage($name) ?? $default ?? $name;
      
        return $this;
    }

    /**
     * Get message
     *
     * @param string $name
     * @return string|null
     */
    public function getMessage(string $name): ?string
    {
        if (isset($this->messages[$name]) == false && $this->messagesLoaded == false) {
            $this->loadMesasgesComponent();
        }

        return $this->messages[$name] ?? Arrays::getValue($this->messages,$name,'.');        
    }

    /**
     * Load messages from html component json file
     *
     * @param string $componentName
     * @param string|null $language
     * @return void
     */
    public function loadMessages(string $componentName): void
    {       
        $this->messagesComponentName = $componentName;
        $this->messagesLoaded = false;
    }

    /**
     * Load messages component
     *
     * @param string|null $language
     * @return void
     */
    protected function loadMesasgesComponent(?string $language = null): void
    {
        if (empty($this->messagesComponentName) == true) {
            return;
        }
        $language = $language ?? $this->getPageLanguage();

        $component = ComponentFactory::create(
            $this->messagesComponentName,
            $language,
            'json',
            Path::VIEW_PATH,
            Path::EXTENSIONS_PATH,
            $this->get('config')['settings']['primaryTemplate'] ?? 'system'
        );
        $component->resolve([]);
        $messages = $component->getProperties();
        
        $this->messages = (empty($messages) == true) ? [] : $messages;    
        $this->messagesLoaded = true;
    }

    /**
     * Get page language
     *
     * @param array $data
     * @param bool $skipSession
     * @return string
    */
    public function getPageLanguage($data = [], bool $skipSession = false): string
    {     
        $language = $data['language'] ?? '';
        if (empty($language) == false) {
            return $language;
        }
        
        $language = Cookie::get('language',null);      
        if (empty($language) == false) {
            return $language;
        } 
       
        if ($skipSession == false) {
            $language = Session::get('language',null);
        }
       
        return (empty($language) == true) ? $this->getDefaultLanguage() : $language;           
    }

    /**
     * Return true if page load is with new language code
     *
     * @param array $data
     * @return boolean
     */
    public function isLanguageChange($data): bool
    {
        if (isset($data['language']) == false) {
            return false;
        }

        return (Session::get('language') != $data['language']);
    }

    /**
     * Get default language
     *
     * @return string
     */
    public function getDefaultLanguage(): string
    {
        return $this->get('config')['settings']['defaultLanguage'] ?? 'en';
    }
}
