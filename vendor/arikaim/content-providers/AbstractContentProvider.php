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

use Arikaim\Core\Content\Traits\ContentProvider;
use Arikaim\Core\Interfaces\Content\ContentProviderInterface;

/**
 *  Abstract content provider class
 */
abstract class AbstractContentProvider implements ContentProviderInterface
{
    use ContentProvider;

    /**
     * Content provider name
     *
     * @var string
     */
    protected $contentProviderName;

    /**
     * Content provider title
     *
     * @var string
     */
    protected $contentProviderTitle;

    /**
     * Content provider category
     *
     * @var string|null
     */
    protected $contentProviderCategory = null;

    /**
     * Content type name or handler class
     *
     * @var string
     */
    protected $contentType;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Get content
     *
     * @param string|int|array $key  Id, Uuid or content name slug
     * @param string|null $contentType  Content type name
     * @param string|array|null $keyFields
     * @return array|null
     */
    abstract public function getContent($key, ?string $contentType = null, $keyFields = null): ?array;    
}
