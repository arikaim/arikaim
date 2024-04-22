<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\Job;

use Closure;

/**
 * Job progress interface
 */
interface JobProgressInterface
{   
    /**
     * Set on job progress callback
     * 
     * @param Closure $callback
     * @return void
    */
    public function onJobProgress(Closure $callback): void;

    /**
     * Set on job progress error callback
     * 
     * @param Closure $callback
     * @return void
    */
    public function onJobProgressError(Closure $callback): void;

    /**
     * Run job progress callback 
     *
     * @param mixed $param
     * @return void
     */
    public function jobProgress($param): void;

    /**
     * Run job progress error callback 
     *
     * @param mixed $param
     * @return void
    */
    public function jobProgressError($param): void;
}
