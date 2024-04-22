<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits;

use Arikaim\Core\Utils\FileType;

/**
 * File type trait
*/
trait FileTypeTrait 
{        
    /**
     * File type scope
     *
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    public function scopeFileTypeQuery($query, string $type)
    {
        $typeInfo = FileType::getFileTypeItem($type);
        if ($typeInfo === false) {
            return false;
        }
        $columnName = $this->getMimeTypeColumnName();
        $start = $typeInfo['start'] + 1;
        $length = $typeInfo['length'];
       
        return $query->whereRaw("SUBSTR(" . $columnName . ",$start,$length) = '$type' ");
    }

    /**
     * Get default column name
     *
     * @return string
     */
    public function getMimeTypeColumnName(): string
    {
        return $this->mimeTypeColumnName ?? 'mime_type';
    }

    /**
     * Get file mime type value
     *
     * @return string|null
     */
    public function getMimeTypeColumnValue()
    {
        $columnName = $this->getMimeTypeColumnName();
        
        return $this->$columnName;
    }

    /**
     * Return true if file have mime type
     *
     * @param string $type one from: image,video,audio,application,text,pdd,font
     * @return boolean
     */
    public function isFileType(string $type): bool
    {
        return FileType::isFileType($type,$this->getMimeTypeColumnValue());       
    }

    /**
     * Get file type 
     *
     * @return string|false
     */
    public function getFileType()
    {
        return FileType::getFileType($this->getMimeTypeColumnValue());       
    }

    /**
     * Return true if file type is zip
     *
     * @return boolean
     */
    public function isZip(): bool
    {
        return $this->isFileType('zip');
    }

    /**
     * Return true if file is image
     *
     * @return boolean
    */
    public function isImage(): bool
    {
        return $this->isFileType('image');
    }

    /**
     * Return true if file type is directory
     *
     * @return boolean
     */
    public function isDirectory()
    {
        return FileType::isDirectory($this->getMimeTypeColumnValue());
    }

    /**
     * Return true if file is video
     *
     * @return boolean
    */
    public function isVideo(): bool
    {
        return $this->isFileType('video');       
    }

    /**
     * Return true if file is audio
     *
     * @return boolean
    */
    public function isAudio(): bool
    {
        return $this->isFileType('audio');  
    }

    /**
     * Return true if file is application
     *
     * @return boolean
    */
    public function isApplication(): bool
    {
        return $this->isFileType('application');
    }

    /**
     * Return true if file is text
     *
     * @return boolean
    */
    public function isText(): bool
    {
        return $this->isFileType('text');
    }

    /**
     * Return true if file is font
     *
     * @return boolean
    */
    public function isFont(): bool
    {
        return $this->isFileType('font');
    }

    /**
     * Return true if file is pdf
     *
     * @return boolean
    */
    public function isPdf(): bool
    {
        return $this->isFileType('pdf');
    }
}
