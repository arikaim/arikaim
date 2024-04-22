<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Routes;

use FastRoute\RouteParser\Std;
use FastRoute\BadRouteException;
use FastRoute\RouteCollector;
use Exception;

/**
 * Routes storage
*/
class Route
{
    /**
     * Return true if route pattern have placeholder
     *
     * @param string $pattern
     * @return boolean
     */
    public static function hasPlaceholder(string $pattern): bool
    {
        return \preg_match('/\{(.*?)\}/',$pattern);
    }

    /**
     * Get language route path  
     *
     * @param string $path
     * @return string
     */
    public static function getLanguagePattern(string $path): string
    {        
        return (\substr($path,-1) == '/') ? '[{language:[a-z]{2}}/]' : '[/{language:[a-z]{2}}/]';
    }

    /**
     * Page route param pattern
     *
     * @param string $path
     * @return string
     */
    public static function getPagePattern(string $path = ''): string
    {
        return (\substr($path,-1) == '/') ? '[{page:\d+}]' : '[/{page:\d+}]';
    }

    /**
     * Get route url
     *
     * @param string $pattern
     * @param array  $data
     * @param array  $queryParams
     * @return string
     */
    public static function getRouteUrl(string $pattern, array $data = [], array $queryParams = []): string
    {      
        if (Self::hasPlaceholder($pattern) == false) {           
            return $pattern;
        }
        $segments = [];      
        $parser = new Std();
        $expressions = \array_reverse($parser->parse($pattern));
         
        foreach ($expressions as $expression) {

            foreach ($expression as $segment) {               
                if (\is_string($segment) == true) {
                    $segments[] = $segment;
                    continue;
                }
                if (\array_key_exists($segment[0],$data) == false) {
                    $segments = [];
                    break;
                }
                $segments[] = $data[$segment[0]];
            }            
            
            if (empty($segments) == false) {
                break;
            }
        }

        if (empty($segments) == true) {
            return $pattern;
        }

        $url = \implode('',$segments);
        if ($queryParams) {
            $url .= '?' . \http_build_query($queryParams);
        }

        return $url;
    }

    /**
     * Return true if route pattern is valid
     *
     * @param string $pattern
     * @return boolean
     */
    public static function isValidPattern(string $pattern): bool
    {
        $parser = new Std();
        try {
            $parser->parse($pattern);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Validate route
     *
     * @param string $method
     * @param string $pattern
     * @param array $routes
     * @return boolean
     */
    public static function validate(string $method, string $pattern, $routes)
    {       
        $collector = Self::createRouteCollector();

        $callback = function (RouteCollector $collector) use($routes,$method,$pattern) {
            foreach($routes as $item) {             
                $collector->addRoute($item['method'],$item['pattern'],$item['name']);
            }
            // add new route
            try {              
                $collector->addRoute($method,$pattern,'new_route');
            } catch (BadRouteException $e) {            
               return false;
            }
            return true;
        };

        return $callback($collector);       
    }

    /**
     * Create route collector
     *
     * @return RouteCollector
     */
    public static function createRouteCollector()
    {
        $options = [
            'routeParser'    => 'FastRoute\\RouteParser\\Std',
            'dataGenerator'  => 'FastRoute\\DataGenerator\\GroupCountBased',
            'dispatcher'     => 'FastRoute\\Dispatcher\\GroupCountBased',
            'routeCollector' => 'FastRoute\\RouteCollector',
        ];

        /** @var RouteCollector $routeCollector */
        $routeCollector = new $options['routeCollector'](
            new $options['routeParser'], new $options['dataGenerator']
        );
        
        return $routeCollector;
    }
}
