<?php

namespace Arikaim\Core\Framework\Router;

use Exception;

/**
 * Route generator class
 */
class RouteGenerator 
{
    /**
     * Static routes
     *
     * @var array
     */
    protected $staticRoutes = [];
    
    /**
     * Routes map
     *
     * @var array
     */
    protected $routesMap = [];

    /**
     * Route parser
     *
     * @var RouteParser
     */
    protected $routeParser;

    /**
     * Constructor
     *
     * @param RouteParser $routeParser
     */
    public function __construct(object $routeParser)
    {
        $this->routeParser = $routeParser;      
    }

    /**
     * Add route
     *
     * @param string $method
     * @param string $pattern
     * @param mixed $handler
     * @param string|int|null $id
     * @return void
     */
    public function addRoute(string $method, string $pattern, $handler, $id = null): void
    {
        $data = $this->routeParser->parse($pattern);

        foreach ($data as $routeData) {
            if (\count($routeData) === 1 && \is_string($routeData[0]) == true) {
                $this->addStaticRoute($method,$routeData,$handler,$id);
            } else {
                $this->addVariableRoute($method,$routeData,$handler,$id);
            }
        }
    }

    /**
     * Get routes data
     *
     * @param string $method
     * @param int $routeType
     * @return array
     */
    public function getData(string $method): array
    {
        // process variable routes
        $variableRoutes = [];
        foreach ($this->routesMap as $method => $regexToRoutesMap) {
            $count = \count($regexToRoutesMap);
            $numParts = \max(1,\round($count / 10));
            $chunkSize = \ceil($count / $numParts);

            $chunks = \array_chunk($regexToRoutesMap,$chunkSize,true);
            $variableRoutes[$method] = \array_map([$this,'processChunk'],$chunks);
        }
 
        return [$this->staticRoutes,$variableRoutes];
    }
    

    /**
     * Add static route
     *
     * @param string $method
     * @param array $data
     * @param mixed $handler
     * @param string|int|null $id
     * @return void
    */
    protected function addStaticRoute(string $method, array $data, $handler, $id = null): void
    {
        $routeStr = $data[0];

        if (isset($this->staticRoutes[$method][$routeStr]) == true) {
            throw new Exception(\sprintf('Route exist "%s" for "%s"',$routeStr,$method));
        }

        if (isset($this->routesMap[$method]) == true) {
            foreach ($this->routesMap[$method] as $route) {               
                $match = (bool)\preg_match('~^' . $route['regex'] . '$~',$routeStr);
                if ($match == true) {
                    throw new Exception(\sprintf('Route "%s" is shadowed by "%s" for "%s"',$routeStr,$route['regex'],$method));
                }
            }
        }

        $this->staticRoutes[$method][$routeStr] = [
            'id'        => $id,
            'methhod'   => $method,
            'handler'   => $handler,
            'regex'     => null,
            'variables' => []          
        ];
    }

    /**
     * Add variable route
     *
     * @param string $method
     * @param array $data
     * @param mixed $handler
     * @param string|int|null $id
     * @return void
     */
    protected function addVariableRoute(string $method, array $data, $handler, $id = null): void
    {
        list($regex,$variables) = $this->buildRegexForRoute($data);

        if (isset($this->routesMap[$method][$regex]) == true) {
            throw new Exception(\sprintf('Cannot register two routes matching "%s" for method "%s"',$regex,$method));
        }

        $this->routesMap[$method][$regex] = [
            'id'        => $id,
            'methhod'   => $method,
            'handler'   => $handler,
            'regex'     => $regex,
            'variables' => $variables          
        ];
    }

    /**
     * Build regexp
     *
     * @param array $data
     * @return array
     */
    private function buildRegexForRoute(array $data): array
    {
        $regex = '';
        $variables = [];
        foreach ($data as $part) {
            if (\is_string($part)) {
                $regex .= \preg_quote($part,'~');
                continue;
            }

            list($varName,$regexPart) = $part;

            if (isset($variables[$varName]) == true) {
                throw new Exception(\sprintf('Cannot use the same placeholder "%s" ',$varName));
            }
            if ($this->regexHasCapturingGroups($regexPart) == true) {
                throw new Exception(\sprintf('Route "%s" parameter "%s" contains group',$regexPart,$varName));
            }

            $variables[$varName] = $varName;
            $regex .= '(' . $regexPart . ')';
        }

        return [$regex,$variables];
    }

    /**
     * Process chunks
     *
     * @param array $regexToRoutesMap
     * @return array
     */
    protected function processChunk(array $data): array
    {
        $routeMap = [];
        $regexes = [];
        $numGroups = 0;

        foreach ($data as $regex => $route) {
            $numVariables = \count($route['variables']);
            $numGroups = \max($numGroups,$numVariables);

            $regexes[] = $regex . \str_repeat('()', $numGroups - $numVariables);
            $routeMap[$numGroups + 1] = $route;
               
            ++$numGroups;
        }

        return [
            'regex'    => '~^(?|' . \implode('|', $regexes) . ')$~', 
            'routeMap' => $routeMap
        ];
    }

    /**
     * Has cap groups
     *
     * @param string $regex
     * @return boolean
     */
    private function regexHasCapturingGroups(string $regex): bool
    {
        if (\strpos($regex,'(') == false) {
            return false;
        }

        return (bool)\preg_match(
            '~
                (?:
                    \(\?\(
                  | \[ [^\]\\\\]* (?: \\\\ . [^\]\\\\]* )* \]
                  | \\\\ .
                ) (*SKIP)(*FAIL) |
                \(
                (?!
                    \? (?! <(?![!=]) | P< | \' )
                  | \*
                )
            ~x',
            $regex
        );
    } 
}
