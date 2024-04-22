<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Queue\Traits;

use Closure;

/**
 * Job progress 
*/
trait JobProgress 
{  
    /**
     * Set on job progress callback
     * 
     * @param Closure|null $callback
     * @return void
    */
    public function onJobProgress(?Closure $callback): void
    {
        $this->onJobProgress = $callback;
    }

    /**
     * Set on job progress error callback
     * 
     * @param Closure|null $callback
     * @return void
    */
    public function onJobProgressError(?Closure $callback): void
    {
        $this->onJobProgressError = $callback;
    }

    /**
     * Get on job progress callback
     *
     * @return Closure|null
     */
    protected function getOnJobProgress()
    {
        return $this->onJobProgress ?? null;
    }
    
    /**
     * Get on job progress error
     *
     * @return Closure|null
     */
    protected function getOnJobProgressError()
    {
        return $this->onJobProgressError ?? null;
    }

    /**
     * Run job progress callback 
     *
     * @param mixed|null $param
     * @return void
     */
    public function jobProgress($param): void
    {
        if (\is_callable($this->getOnJobProgress()) == true) {
            ($this->onJobProgress)($param);           
        }
    } 

    /**
     * Run job progress error callback 
     *
     * @param mixed $param
     * @return void
    */
    public function jobProgressError($param): void
    {
        if (\is_callable($this->getOnJobProgressError()) == true) {
            ($this->onJobProgressError)($param);           
        }
    } 
}
