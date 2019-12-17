<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Api\Ui;

use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\Packages\Library\LibraryRepository;
use Arikaim\Core\Utils\Path;

/**
 * Ui library upload Api controller
*/
class Library extends ApiController
{
    /**
     * Get html component details
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function upload($request, $response, $data)
    {
        // control panel only
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) use($request) {             
            $files = $request->getUploadedFiles();
            if (isset($files['file']) == true) {
                $file = $files['file'];
            } else {
                $this->withError("Upload file error")->getResponse();           
            }

            if ($file->getError() === UPLOAD_ERR_OK) {
                $fileName = $file->getClientFilename();
                $dstination = Path::STORAGE_TEMP_PATH . $file->getClientFilename();
                $file->moveTo($dstination);

                $result = LibraryRepository::unpack($dstination);
                if ($result == false) {
                    $this->setError("Not valid zip arhive");
                }
            }

            $this->setResult(['file_name' => $fileName]); 
        });
        $data->validate();
    }
}
