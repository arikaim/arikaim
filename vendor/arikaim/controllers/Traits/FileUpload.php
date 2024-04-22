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

use Arikaim\Core\Utils\Path;
use Arikaim\Core\Utils\File;
use Closure;

/**
 * File upload trait
*/
trait FileUpload 
{        
    /**
     * Before upload
     *
     * @var Closure|null
     */
    protected $beforeUploadCallback = null;

    /**
     * After upload
     *
     * @var Closure|null
     */
    protected $afterUploadCallback = null;

    /**
     * Set before upload
     *
     * @param Closure $callback
     * @return void
     */
    protected function onBeforeUpload(Closure $callback): void
    {
        $this->beforeUploadCallback = $callback;
    }

    /**
     * Set after upload
     *
     * @param Closure $callback
     * @return void
     */
    protected function onAfterUpload(Closure $callback): void
    {
        $this->afterUploadCallback = $callback;
    }

    /**
     * Get file upload message name
     *
     * @return string
     */
    protected function getFileUploadMessage(): string
    {
        return $this->fileUploadMessage ?? 'upload';
    }

    /**
     * Get field name
     *
     * @return string
     */
    public function getUplaodFieldName(): string
    {
        return $this->uploadFiledName ?? 'file';
    }

    /**
     * File upload
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function uploadController($request, $response, $data)
    { 
        $data->validate(true);      
        // before upload
        $data = $this->resolveFileUploadCallback($data,$this->beforeUploadCallback);

        $destinationPath = $data->get('path','');
        $files = $this->uploadFiles($request,$destinationPath);
        
        // after upload
        $data = $this->resolveFileUploadCallback($data,$this->afterUploadCallback);

        $this
            ->message($this->getFileUploadMessage())
            ->field('files',$files);                                           
    }

    /**
     * Upload file(s)
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string $path Destination path relative to storage path
     * @param boolean $relative
     * @param boolean $moveFile
     * @param string|null $destinationFileName
     * @return array
     */
    public function uploadFiles(
        $request, 
        string $path = '', 
        bool $relative = true, 
        bool $moveFile = true,
        ?string $destinationFileName = null
    ): array
    {
        $fieldName = $this->getUplaodFieldName();
        $files = $request->getUploadedFiles();
        $destinationPath = ($relative == true) ? Path::STORAGE_PATH . $path : $path;
        $uploadedFiles = (\is_object($files[$fieldName]) == true) ? [$files[$fieldName]] : $files[$fieldName];
    
        $result = [];
        foreach ($uploadedFiles as $file) {
            $clientFileName = $file->getClientFilename();
            $destinationFileName = (empty($destinationFileName) == true) ? $clientFileName : File::replaceFileName($clientFileName,$destinationFileName);

            if ($file->getError() === UPLOAD_ERR_OK) {                   
                $fileName = $destinationPath . $destinationFileName;   
                if ($moveFile == true) {
                    $file->moveTo($fileName);       
                }         
            }

            $result[] = [
                'name'       => $destinationFileName,              
                'size'       => $file->getSize(),              
                'file_name'  => $destinationFileName, 
                'media_type' => $file->getClientMediaType(),
                'moved'      => $moveFile,
                'error'      => $file->getError()
            ];         
        }

        return $result;
    }

    /**
     * Resolve callback
     *
     * @param mixed $data
     * @param Closure|null $callback
     * @return mixed
     */
    private function resolveFileUploadCallback($data, ?Closure $callback)
    {
        return (\is_callable($callback) == true) ? $callback($data) : $data;         
    }
}
