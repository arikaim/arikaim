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

use Arikaim\Core\Interfaces\Content\FieldInterface;

/**
 * Content type interface
 */
interface ContentTypeInterface
{      
    /**
     * Get action handlers list
     *
     * @return array
     */
    public function getActionHandlers(): array;
    
    /**
     * Get title fields
     *
     * @return array
     */
    public function getTitleFields(): array;
    
    /**
     * Get searchable field names
     *
     * @return array
     */
    public function getSearchableFieldNames(): array;

    /**
     * Get field names
     *
     * @return array
     */
    public function getFieldNames(): array;

    /**
     * Get content type fields
     *
     * @return array
     */
    public function getFields(): array;

    /**
     * Get content type actions
     *
     * @return array
     */
    public function getActions(): array;

    /**
     * Get field
     *
     * @param string $name
     * @return FieldInterface|null
    */
    public function getField(string $name): ?FieldInterface;

    /**
     * Get content type title
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Get content type name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get content type name
     *
     * @return string|null
     */
    public function getCategory(): ?string;
}
