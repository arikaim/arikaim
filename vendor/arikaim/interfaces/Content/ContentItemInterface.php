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
 * Content item interface
 */
interface ContentItemInterface
{   
    /**
     * Get content type
     *
     * @return ContentTypeInterface
     */
    public function getType(): ContentTypeInterface;

    /**
     * Run action
     *
     * @param string $name
     * @param array|null $options
     * @return void
     */
    public function runAction(string $name, ?array $options = []);

    /**
     * Get content item id
     *
     * @return int|string
     */
    public function getId();

    /**
     * Get fields
     *
     * @return array
     */
    public function fields(): array;

    /**
     * Get field value
     *
     * @param string $fieldName
     * @param mixed $default
     * @return mixed
     */
    public function getValue(string $fieldName, $default = null);

    /**
     * Set field value
     *
     * @param string $fieldName
     * @param mixed $value
     * @return void
     */
    public function setValue(string $fieldName, $value): void;

    /**
     * Get content field
     *
     * @param string $fieldName
     * @return FieldInterface|null
     */
    public function field(string $fieldName): ?FieldInterface;
}
