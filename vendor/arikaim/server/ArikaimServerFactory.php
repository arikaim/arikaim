<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Server;

use Arikaim\Core\Server\ServerInterface;

/**
 * Arikaim server factory 
 */
class ArikaimServerFactory 
{  
    const DEFAULT_SERVER_TYPE = 'services';
    const DEFAULT_SERVER_LIB  = 'swoole';

    const SERVER_TYPE = [
        'http',
        'services',
        'websocket'
    ];

    /**
     * Server classes list
     */
    const SERVERS_LIST = [
        'http' => [
            'swoole' => 'Arikaim\\Core\\Server\\Swoole\\HttpServer'           
        ],
        'services' => [
            'swoole' => 'Arikaim\\Core\\Server\\Swoole\\ServicesServer'
        ],
        'websocket' => [
            'swoole' => 'Arikaim\\Core\\Server\\Swoole\\WebSocketServer'
        ]
    ];

    /**
     * Create server instance
     *
     * @param string|null $type
     * @param string|null $serverLib
     * @return ServerInterface|null
     */
    public static function create(?array $options = null): ?ServerInterface
    {
        $options = ($options == null) ? Self::getConsoleOptions() : $options;

        $type = $options['type'] ?? Self::DEFAULT_SERVER_TYPE;
        $serverLib = $options['lib'] ?? Self::DEFAULT_SERVER_LIB;
        $host = $options['host'] ?? null;
        $port = $options['port'] ?? null;

        if (\in_array($type,Self::SERVER_TYPE) == false) {
            echo 'Not vlaid server type' . PHP_EOL;
            return null;
        }

        $serverClass = Self::SERVERS_LIST[$type][$serverLib] ?? null;

        if (empty($serverClass) == true) {         
            return null;
        }

        $server = new $serverClass($host,$port,$options);

        return $server;
    }

    /**
     * Get console options
     *
     * @return array
     */
    public static function getConsoleOptions(): array
    {
        $console = \getopt("c:t:h:p:l:a:");
        $configFile = $console['c'] ?? null;

        if (empty($configFile) == false) {
            $result = Self::loadConfigFile((string)$configFile);
            if (\is_array($result) == true) {
                return $result;
            }
        }
        
        $options['type'] = $console['t'] ?? 'services';
        $options['host'] = $console['h'] ?? null;
        $options['port'] = $console['p'] ?? null;
        $options['lib']  = $console['l'] ?? 'swoole';
        $options['appClass'] = $console['a'] ?? null;

        return $options;
    }

    /**
     * Load config file
     *
     * @param string $fileName
     * @return array|null
     */
    public static function loadConfigFile(string $fileName): ?array
    {
        $fileName = ROOT_PATH . DIRECTORY_SEPARATOR . $fileName;

        return (\file_exists($fileName) == true) ? include($fileName) : null;          
    }
}
