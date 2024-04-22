<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Server\Swoole;

/**
 * Parse uploaded files
 */
class UploadedFiles
{
    /**
     * Parse uploaded files
     *
     * @param array $files
     * @param object $factory
     * @return array
     */
    public static function parse(array $files, $factory): array
    {       
        $result = [];

        foreach ($files as $field => $file) {
            if (!isset($file['error'])) {
                if (\is_array($file)) {
                    $result[$field] = Self::parse($file,$factory);
                }
                continue;
            }

            $result[$field] = [];
            if (\is_array($file['error']) == false) {
                $parsed[$field] = $factory->createUploadedFile(
                    $factory->createStreamFromFile($file['tmp_name']),
                    $file['size'] ?? null,
                    $file['error'],
                    $file['name'] ?? null,
                    $file['type'] ?? null
                );
            } else {
                $items = [];
                $errors = \array_keys($file['error']);
                foreach ($errors as $key) {
                    $items[$key]['name'] = $file['name'][$key];
                    $items[$key]['type'] = $file['type'][$key];
                    $items[$key]['tmp_name'] = $file['tmp_name'][$key];
                    $items[$key]['error'] = $file['error'][$key];
                    $items[$key]['size'] = $file['size'][$key];

                    $result[$field] = Self::parse($items,$factory);
                }
            }
        }

        return $result;
    }
}
