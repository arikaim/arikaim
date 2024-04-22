<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Controllers\Traits;

/**
 * Task progress trait
*/
trait TaskProgress 
{        
    /**
     * Current task progress step
     *
     * @var integer
     */
    protected $progressStep = 0;

    /**
     * Total task progress steps null for unknow
     *
     * @var int|null
     */
    protected $totalProgressSteps = null;

    /**
     * Delay value
     *
     * @var int
     */
    protected $progressSleep = 1;

    /**
     * Init task progress response 
     *
     * @param int|null $totalSteps  
     * @param int $sleep  
     * @return void
     */
    public function initTaskProgress(?int $totalSteps = null, int $sleep = 1): void
    {
        \ini_set('output_buffering','Off'); 
        \ini_set('zlib.output_compression',0);       
        \ini_set('implicit_flush',true);
        \ob_implicit_flush(true);

        $this->progressStep = 1;
        $this->progressSleep = $sleep; 
        $this->totalProgressSteps = $totalSteps;
      
        \header('Connection: close;');
        \header('Content-Encoding: none;');   
        \header('Cache-Control: no-cache'); 
    }

    /**
     * Set end task progress
     *
     * @return void
     */
    public function taskProgressEnd(): void
    {
        @ob_end_flush();
       
        $this->clearResult();
        $this->field('progress_end',true);
    }

    /**
     * Flush progress response
     *
     * @return void
     */
    public function sendProgressResponse(): void
    {   
        // set task progress field
        $this->field('progress',true);
        $this->field('progress_step',$this->progressStep);
        $this->field('progress_total_steps',$this->totalProgressSteps);

        $response = $this->getResponse(false,null,true);
        $body = $response->getBody();
               
        echo $body;
          
        \flush();

        $this->progressStep++;
        $this->clearResult();  

        \sleep($this->progressSleep);         
    }
}
