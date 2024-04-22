<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Utils;

/**
 * File type
*/
class FileType
{
    /**
     * File type items
     *
     * @var array
     */
    private static $filesType = [
        'image' => [
            'start'  => 0,
            'length' => 5
        ],
        'video' => [
            'start'  => 0,
            'length' => 5
        ],
        'audio' => [
            'start'  => 0,
            'length' => 5
        ],
        'pdf' => [
            'start'  => -3,
            'length' => 3
        ],       
        'text' => [
            'start'  => 0,
            'length' => 4
        ],
        'font' => [
            'start'  => 0,
            'length' => 4
        ],       
        'zip' => [
            'items' => [
                'application/zip',
                'application/x-zip-compressed',
                'multipart/x-zip'
            ]
        ],
        'application' => [
            'start'  => 0,
            'length' => 11
        ],
    ];

    /**
     * Get file type item info 
     *
     * @param string $type
     * @return array|false
     */
    public static function getFileTypeItem(string $type)
    {
        return (isset(Self::$filesType[$type]) == false) ? false : Self::$filesType[$type];         
    }

    /**
     * Return true if file have mime type
     *
     * @param string $type one from: image,video,audio,application,text,pdd,font
     * @param string $mimeType
     * @return boolean
     */
    public static function isFileType(string $type, string $mimeType): bool
    {
        if (isset(Self::$filesType[$type]) == false) {
            return false;
        }
    
        if (isset(Self::$filesType[$type]['items']) == true) {            
            return \in_array($mimeType,Self::$filesType[$type]['items']);
        }

        return (\substr($mimeType,Self::$filesType[$type]['start'],Self::$filesType[$type]['length']) == $type);
    }

    /**
     * Get file type 
     * 
     * @param string $mimeType
     * @return string|false
     */
    public static function getFileType(string $mimeType)
    {
        foreach (Self::$filesType as $key => $item) {
            $type = Self::isFileType($key,$mimeType);
            if ($type == true) {
                return $key;
            }
        }

        return Self::isDirectory($mimeType);
    }

    /**
     * Return true if file type is zip
     *
     * @param string $mimeType
     * @return boolean
     */
    public static function isZip(string $mimeType): bool
    {
        return Self::isFileType('zip',$mimeType);
    }

    /**
     * Return true if file is image
     *
     * @param string $mimeType
     * @return boolean
    */
    public static function isImage(string $mimeType): bool
    {
        return Self::isFileType('image',$mimeType);
    }

    /**
     * Return true if file type is directory
     *
     * @param string $mimeType
     * @return boolean
     */
    public static function isDirectory(string $mimeType): bool
    {
        return ($mimeType == 'directory');
    }

    /**
     * Return true if file is video
     *
     * @param string $mimeType
     * @return boolean
    */
    public static function isVideo(string $mimeType): bool
    {
        return Self::isFileType('video',$mimeType);       
    }

    /**
     * Return true if file is audio
     *
     * @param string $mimeType
     * @return boolean
    */
    public static function isAudio(string $mimeType): bool
    {
        return Self::isFileType('audio',$mimeType);  
    }

    /**
     * Return true if file is application
     *
     * @param string $mimeType
     * @return boolean
    */
    public static function isApplication(string $mimeType): bool
    {
        return Self::isFileType('application',$mimeType);
    }

    /**
     * Return true if file is text
     *
     * @param string $mimeType
     * @return boolean
    */
    public static function isText(string $mimeType): bool
    {
        return Self::isFileType('text',$mimeType);
    }

    /**
     * Return true if file is font
     *
     * @param string $mimeType
     * @return boolean
    */
    public static function isFont(string $mimeType): bool
    {
        return Self::isFileType('font',$mimeType);
    }

    /**
     * Return true if file is pdf
     *
     * @param string $mimeType
     * @return boolean
    */
    public static function isPdf(string $mimeType): bool
    {
        return Self::isFileType('pdf',$mimeType);
    }
}
