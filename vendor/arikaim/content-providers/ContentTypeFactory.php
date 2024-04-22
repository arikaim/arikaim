<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Content;

use Arikaim\Core\Interfaces\Content\ContentTypeInterface;
use Arikaim\Core\Utils\Path;

/**
 *  Content type factory
 */
class ContentTypeFactory 
{
    /**
     *  Default content types registry config file name
    */
    const CONTENT_TYPES_FILE_NAME = Path::CONFIG_PATH . 'content-types.php';

    /**
     * Create content type
     * 
     * @param string $name
     * @return ContentTypeInterface|null
     */
    public static function create(string $name): ?ContentTypeInterface
    {
        $contentTypes = (\file_exists(Self::CONTENT_TYPES_FILE_NAME) == true) ? include(Self::CONTENT_TYPES_FILE_NAME) : null;  
        $item = $contentTypes[$name] ?? null;
        if (empty($item) == true) {
            return null;
        }

        $contentType = new $item['handler']();
        $contentType->setActionHandlers($item['actions'] ?? []);

        return $contentType;
    }

    /**
     * Check if content type exists
     *
     * @param string $name
     * @return boolean
     */
    public static function has(string $name): bool
    {
        return !empty(Self::create($name));
    } 
}
