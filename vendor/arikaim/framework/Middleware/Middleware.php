<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;

use Arikaim\Core\Framework\MiddlewareInterface;

/**
 *  Middleware base class
 */
abstract class Middleware implements MiddlewareInterface
{
    /**
     * Middleware options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Container
     *
     * @var ContainerInterface|null
     */
    protected $container = null;

    /**
     * Process middleware 
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return array [$request,$response]
     */
    abstract public function process(ServerRequestInterface $request, ResponseInterface $response): array; 

    /**
     * Constructor
     *
     * @param ContainerInterface|null
     * @param array|null $options
     */
    public function __construct(?ContainerInterface $container = null, ?array $options = [])
    {
        $this->container = $container;
        $this->options = $options ?? [];
    }
    
    /**
     * Get option value
     *
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function getOption(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * Set option
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setOption(string $name, $value): void
    {
        $this->options[$name] = $value;        
    }

    /**
     * Set option
     *
     * @param string $name
     * @param mixed $value
     * @return Middleware
     */
    public function withOption(string $name, $value)
    {
        $this->setOption($name,$value);
        
        return $this;
    }

    /**
     * Return all options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
