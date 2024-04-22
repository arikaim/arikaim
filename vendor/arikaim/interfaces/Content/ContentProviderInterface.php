<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\Content;

use Arikaim\Core\Interfaces\Content\ContentTypeInterface;

/**
 * Content provider interface
 */
interface ContentProviderInterface
{   
    /**
     * Create new content item
     *
     * @param array $data
     * @param string|null $contentType  Content type name
     * @return array|null
     */
    public function createItem(array $data, ?string $contentType = null): ?array;

    /**
     * Save content item
     *
     * @param string|int $key
     * @param array $data
     * @param string|null $contentType  Content type name
     * @return boolean
     */
    public function saveItem($key, array $data, ?string $contentType = null): bool;

    /**
     * Get provider name
     *
     * @return string
     */
    public function getProviderName(): string;

    /**
     * Get provider category
     *
     * @return string|null
     */
    public function getProviderCategory(): ?string;

    /**
     * Get provider title
     *
     * @return string|null
     */
    public function getProviderTitle(): ?string;

    /**
     * Get content
     *
     * @param string|int|array $key  Id, Uuid or content name slug
     * @param string|null $contentType  Content type name
     * @param string|array|null $keyFields
     * @return array|null
     */
    public function getContent($key, ?string $contentType = null, $keyFields = null): ?array;

    /**
     * Get content type
     *    
     * @return ContentTypeInterface|null
     */
    public function getContentType(): ?ContentTypeInterface;

    /**
     * Get content type
     *    
     * @param ContentTypeInterface $contentType
     * @return void
     */
    public function setContentType(ContentTypeInterface $contentType): void;

    /**
     * Get supported content types
     *
     * @return array
    */
    public function getSupportedContentTypes(): array;

    /**
     * Get content list
     *
     * @param mixed|null $filter
     * @param integer $page
     * @param integer $perPage
     * @return array[ContentItemInterface]
    */
    public function getContentItems($filter = null, int $page = 1, int $perPage = 20): ?array;
}
