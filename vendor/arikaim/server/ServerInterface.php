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
 * Server interface
 */
interface ServerInterface
{     
    /**
     * Run server
     *    
     * @return void
     */
    public function run(): void;

    /**
     * Boot server
     *    
     * @return void
     */
    public function boot(): void;

    /**
     * Stop server
     *    
     * @return void
     */
    public function stop(): void;
}
