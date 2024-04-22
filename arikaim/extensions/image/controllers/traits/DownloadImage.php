<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Image\Controllers\Traits;

use Arikaim\Core\Db\Model;

/**
 * Download image trait
*/
trait DownloadImage
{
    /**
     * Download image
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function downloadImage($request, $response, $data)
    {
        $uuid = $data->get('uuid',null);
        $image = Model::Image('image')->findById($uuid);
        // not valid image uuid or id 
        if ($image == null) {
            $this->error('errors.id','Not valid image id.');
            return false;
        }
  
        if ($image->status == $image->DISABLED()) {
            $this->error('errors.status','Image is diabled.');
            return false;
        }

        if ($image->private == true) {
            // check access
            $this->requireUserOrControlPanel($image->user_id);
        }
        
        if ($this->get('storage')->has($image->file_name,'storage') == false) {
            $this->error('errors.file','Image file not exist.');
            return false;
        }

        return $this->downloadFile($response,$image->file_name,'storage');       
    }
}
