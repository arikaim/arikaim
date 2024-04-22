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

use Swoole\HTTP\Request;
use Swoole\WebSocket\Server;

use Arikaim\Core\Server\AbstractServer;
use Arikaim\Core\Server\ServerInterface;
use Arikaim\Core\Server\WebSocketAppInterface;
use Exception;

/**
 * Arikaim web socket swoole server 
 */
class WebSocketServer extends AbstractServer implements ServerInterface
{  
    /**
     * Http server swoole instance
     *
     * @var Swoole\HTTP\Server|null
     */
    private $server;

    /**
     * Web socket app instance
     *
     * @var WebSocketAppInterface|null
     */
    private $webSocketApp;

    /**
     * Constructor
     *
     * @param string $host
     * @param string $port
     * @param array $options
     */
    public function __construct(?string $host = null, ?string $port = null, array $options = [])
    {
        parent::__construct($host,$port,$options);

        $appClass = $this->getOption('appClass');

        if (empty($appClass) == true) {
            throw new Exception('Not valid web socket app class',1);
        }

        $this->webSocketApp = new $appClass();
        if (($this->webSocketApp instanceof WebSocketAppInterface) == false) {
            throw new Exception('Web socket app instance not implement interface WebSocketAppInterface',1);
        }
    }

    /**
     * Boot server
     *
     * @return void
    */
    public function boot(): void
    {
        $this->server = new Server($this->host,$this->port);
        
        $this->server->set([
            'open_http_protocol' => true
        ]);
    
        // server start
        $this->server->on('start',function($server) {
            echo 'WebSocket server is started at ' . $this->hostToString() . PHP_EOL;
        });
        
        $this->server->on('request', function ($request, $response) {
            $response->header('Access-Control-Allow-Origin','*');
            $response->header('Access-Control-Allow-Headers','*, Authorization');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
            $response->header('Access-Control-Allow-Credentials','true');
            $response->header('Content-Type', 'text/html');

            $response->end('Web socket server.');
        });
       
        // connection open
        $this->server->on('open',function($server, $request) {     
            $this->webSocketApp->onOpen($server,$request);   
            return true;              
        });

        // received message
        $this->server->on('Message',function($server, $frame) {  
            $this->webSocketApp->onMessage($server,$frame);      
            return true;   
        });

        // colse connection
        $this->server->on('close',function($server, int $fd) {   
            $this->webSocketApp->onClose($server,$fd);   
            return true;              
        });

        // disconnected
        $this->server->on('disconnect',function($server, int $fd) {  
            $this->webSocketApp->onDisconnect($server,$fd);   
            return true;           
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
