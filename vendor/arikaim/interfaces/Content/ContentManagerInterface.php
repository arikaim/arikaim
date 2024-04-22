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

use Arikaim\Core\Interfaces\Content\ContentProviderInterface;

/**
 * Content manager interface
 */
interface ContentManagerInterface
{   
    /**
     * Get content providers list
     *
     * @param string|null $category
     * @param string|null $contentType
     * @return array
     */
    public function getProviders(?string $category, ?string $contentType = null): array;

    /**
     * Register content provider
     *
     * @param object|string $provider
     * @return boolean
     */
    public function registerProvider($provider): bool;

    /**
     * Unregister content provider
     *
     * @param string $name
     * @return boolean
     */
    public function unRegisterProvider(string $name): bool;

    /**
     * Get content provider
     * 
     * @param string $name
     * @param string|null $contentType
     * @return ContentProviderInterface|null
     */
    public function provider(string $name, ?string $contentType = null): ?ContentProviderInterface;

    /**
     * Check if provider exists
     *
     * @param string $name
     * @return boolean
     */
    public function hasProvider(string $name): bool;
}
