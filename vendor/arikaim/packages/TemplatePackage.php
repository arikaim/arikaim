<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Packages;

use Arikaim\Core\Packages\Interfaces\PackageInterface;
use Arikaim\Core\Packages\Interfaces\ViewComponentsInterface;
use Arikaim\Core\Packages\Package;
use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\Http\Url;
use Arikaim\Core\Utils\File;
use Arikaim\Core\Collection\Collection;
use DirectoryIterator;

use Arikaim\Core\Packages\Traits\ViewComponents;
use Arikaim\Core\Packages\Traits\CssFiles;
use Arikaim\Core\Packages\Traits\JsFiles;
use Arikaim\Core\Packages\Traits\ComponentTranslations;
use Arikaim\Core\Packages\Traits\Themes;

/**
 * Template package 
*/
class TemplatePackage extends Package implements PackageInterface, ViewComponentsInterface
{
    use    
        ViewComponents,
        Themes,
        CssFiles,
        JsFiles,
        ComponentTranslations;

    /**
     * Get package properties
     *
     * @param boolean $full
     * @return Collection
     */
    public function getProperties(bool $full = false)
    {
        global $arikaim;

        $this->properties['icon'] = $this->properties->get('icon',null); 
        if ($full == true) {              
            $this->viewPath = $this->getPath() . $this->getName() . DIRECTORY_SEPARATOR;
            $this->properties['path'] = $this->viewPath;
            $this->properties['components_path'] = $this->getComponentsPath();
            $this->properties['pages_path'] = $this->getPagesPath();
            $this->properties['routes'] = $arikaim->get('routes')->getRoutes(['template_name' => $this->getName()]);
            $this->properties['pages'] = $this->getPages();
            $this->properties['components'] = $this->getComponentsRecursive();
            $this->properties['emails'] = $this->getEmails();
            $this->properties['macros'] = $this->getMacros();

            $primaryTemplate = $arikaim->get('config')->getByPath('settings/primaryTemplate',null);
            $this->properties['primary'] = ($primaryTemplate == $this->getName());
        }

        return $this->properties; 
    }

    /**
     * Set package as primary
     *
     * @return boolean
     */
    public function setPrimary(): bool
    {
        global $arikaim;

        $arikaim->get('config')->setValue('settings/primaryTemplate',$this->getName());
        $arikaim->get('config')->setValue('settings/templateTheme',$this->getDefautTheme());
        $arikaim->get('config')->save();

        return true;
    }

    /**
     * Install template package
     *
     * @param boolean|null $primary Primary package replaces routes or other params
     * @return mixed
     */
    public function install(?bool $primary = null)
    {
        global $arikaim;

        $routes = $this->getRoutes();

        // install template routes
        $routesAdded = 0;
        $primaryTemplate = $arikaim->get('config')->getByPath('settings/primaryTemplate','system');
        $primary = (empty($primary) == true) ? ($this->getName() == $primaryTemplate) : $primary;

        foreach ($routes as $item) {
            $route = Collection::create($item);
            if ($route->isEmpty('path') == true) {             
                continue;
            }

            if ($route->isEmpty('handler') == false) {    
                $handlerClass = Factory::getExtensionControllerClass($route->getByPath('handler/extension'),$route->getByPath('handler/class'));
            } else {
                $handlerClass = Factory::getControllerClass('Controller');
            }
            //       
            $handlerParams = $route->getByPath('handler/params',null); 
            $handlerMethod = $route->getByPath('handler/method',null);
            $pageName = ($route->isEmpty('page') == false) ? $this->getName() . ':' . $route['page'] : null;
            $auth = $route->getByPath('access/auth',null);
            $auth = $arikaim->get('access')->resolveAuthType($auth);
            $redirect = $route->getByPath('access/redirect',null);
            $languagePath = $route->get('language-path',false); 
            $pattern = $route['path']; 
            // Route type
            $type = ($route->get('home',false) == false) ? 1 : 3; 
                    
            $result = $arikaim->get('routes')->saveTemplateRoute(
                $pattern,
                $handlerClass,
                $handlerMethod,
                $this->getName(),
                $pageName,
                $auth,
                $primary,
                $redirect,
                $type,
                $languagePath
            );
            if ($result != false) {
                $routesAdded++;
                if (empty($handlerParams) == false) {
                    $arikaim->get('routes')->saveRouteOptions('GET',$pattern,$handlerParams);
                }
            }
        }

        // check theme 
        $theme = $arikaim->get('config')->getByPath('settings/templateTheme',null);
        if (empty($theme) == true) {                    
            $arikaim->get('config')->setValue('settings/templateTheme',$this->getDefautTheme());
            $arikaim->get('config')->save();           
        }

        // content
        $content = $this->getContentItems();

        foreach ($content as $item) {
            $key = $item['key'] ?? null;
            $type = $item['type'] ?? null;
            if (empty($key) == true || empty($type) == true) continue;

            $arikaim->get('content')->saveContentItem($key,$type,$item['title'] ?? '');
        }

        return true;          
    }
    
    /**
     * Build css template files
     *
     * @return boolean
     */
    public function buildAssets(): bool
    {        
        global $arikaim;

        $cssPath = Path::TEMPLATES_PATH . $this->getName() . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR;
        $twig = $arikaim->get('view')->createEnvironment([$cssPath]);
        $params = [
            'template_url' => Url::TEMPLATES_URL . '/' . $this->getName() . '/'
        ];

        @File::setWritable($cssPath);

        if (File::isWritable($cssPath) == false) {
            return false;
        }
        
        foreach (new DirectoryIterator($cssPath) as $file) {
            if ($file->isDot() == true || $file->isDir() == true) {
                continue;
            }            
            $fileName = $file->getFilename();
            $tokens = \explode('.',$fileName);
          
            if (isset($tokens[1]) == true) {
                if ($tokens[1] == 'html') {
                    $code = $twig->render($fileName,$params);
                    $cssfileName = \str_replace('.html','',$fileName);
                    File::write($cssPath . $cssfileName,$code);
                }
            }           
        }

        return true;
    }
    
    /**
     * Uninstall package
     *
     * @return bool
     */
    public function unInstall(): bool 
    {
        global $arikaim;

        return $arikaim->get('routes')->deleteRoutes(['template_name' => $this->getName()]);
    }

    /**
     * Enable package
     *
     * @return bool
     */
    public function enable(): bool 
    {
        return true;
    }

    /**
     * Disable package
     *
     * @return bool
     */
    public function disable(): bool 
    {
        return false;
    }   

    /**
     * Get template routes
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->properties->get('routes',[]);
    }

    /**
     * Get content items
     *
     * @return array
     */
    public function getContentItems(): array
    {
        return $this->properties->get('content',[]);
    }
}
