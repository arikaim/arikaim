<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces;

/**
 * Queue worker interface
 */
interface WorkerInterface
{     
    /**
     * Run worker
     *    
     * @return void
     */
    public function run(): void;

    /**
     * Init worker
     *    
     * @return void
     */
    public function init(): void;

    /**
     * Stop worker
     *    
     * @return void
     */
    public function stop(): void;
}
