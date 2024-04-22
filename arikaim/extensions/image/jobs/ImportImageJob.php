<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Image\Jobs;

use Arikaim\Core\Queue\Jobs\Job;

use Arikaim\Core\Interfaces\Job\JobInterface;

/**
 * Fetch image job
 */
class ImportImageJob extends Job implements JobInterface
{
    /**
     * Run job
     *
     * @return mixed
     */
    public function execute()
    {       
        global $container;

        $url = $this->params['url'] ?? null;
        $destination = $this->params['destination'] ?? null;
        if (empty($url) == true || empty($destination) == true) {
            return false;
        }

        return $container->get('service')->get('image.library')->import($url,$destination);
    }    
}
