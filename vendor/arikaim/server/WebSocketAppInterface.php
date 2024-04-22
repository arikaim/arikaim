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

/**
 * Webb socket server application interface
 */
interface WebSocketAppInterface
{     
    /**
     * On connection open
     *
     * @param mixed $server
     * @param mixed $request
     * @return void
     */
    public function onOpen($server, $request): void;

    /**
    * On received message
    *
    * @param mixed $server
    * @param mixed $data
    * @return void
    */
    public function onMessage($server, $data): void;

    /**
     * On connection close
     *
     * @param mixed $server
     * @param mixed $data
     * @return void
     */
    public function onClose($server, $data): void;

    /**
     * On disconnect
     *
     * @param mixed $server
     * @param mixed $data
     * @return void
     */
    public function onDisconnect($server, $data): void;
}
