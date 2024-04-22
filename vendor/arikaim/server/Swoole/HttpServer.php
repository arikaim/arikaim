<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Server\Swoole;

use Nyholm\Psr7\Factory\Psr17Factory;
use Swoole\HTTP\Request;
use Swoole\HTTP\Response;
use Swoole\HTTP\Server;

use Arikaim\Core\Server\AbstractServer;
use Arikaim\Core\Server\Swoole\RequestConverter;
use Arikaim\Core\Server\Swoole\ResponseConverter;
use Arikaim\Core\Server\ServerInterface;
use Arikaim\Core\Arikaim;
use Arikaim\Core\Validator\ValidatorStrategy;
use Arikaim\Core\Http\Session;

/**
 * Http swoole server 
 */
class HttpServer extends AbstractServer implements ServerInterface
{  
    /**
     * Http server swoole instance
     *
     * @var Swoole\HTTP\Server|null
     */
    private $server;

    /**
     * Boot server
     *
     * @return void
    */
    public function boot(): void
    {
        $this->server = new Server($this->host,$this->port);
        $factory = new Psr17Factory();
    
        // Session init
        Session::start();
                             
        // server start
        $this->server->on('start',function (Server $server) {
            echo 'Http server is started at ' . $this->hostToString() . PHP_EOL;
        });

        // server request
        $this->server->on('request',function(Request $request, Response $response) use($factory) {          
            $GLOBALS['APP_START_TIME'] = \microtime(true);

            $psrRequest = RequestConverter::convert($request,$factory);
            $psrResponse = Arikaim::$app->handle($psrRequest);         
            ResponseConverter::convert($psrResponse,$response)->end();          
        });

        // server stop
        $this->server->on('shutdown',function($server, $workerId) {
            echo 'Http server shutdown ' . PHP_EOL;
        });
    }

    /**
     * Run server
     *
     * @return void
     */
    public function run(): void
    {
        $this->server->start();
    }

    /**
     * Stop server
     *    
     * @return void
     */
    public function stop(): void
    {
        $this->server->stop();
    }
}
