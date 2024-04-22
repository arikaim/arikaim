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

/**
 * Field interface
 */
interface FieldInterface
{      
    const TEXT         = 'text';
    const NUMBER       = 'number';
    const CUSTOM       = 'custom';
    const BOOLEAN_TYPE = 'bool';
    const LIST         = 'list';
    const PHP_CLASS    = 'php.class';
    const PASSWORD     = 'password';
    const URL          = 'url';
    const EMAIL        = 'email';
    const TEXT_AREA    = 'text.area';
    const IMAGE        = 'image';
    const DATE         = 'date';

    /**
     * Field type list
     */
    const TYPES_LIST = [
        Self::TEXT,
        Self::NUMBER,
        Self::CUSTOM,
        Self::BOOLEAN_TYPE,
        Self::LIST,
        Self::PHP_CLASS,
        Self::PASSWORD,
        Self::URL,
        Self::EMAIL,
        Self::TEXT_AREA,
        Self::DATE,
        Self::IMAGE
    ];

    /**
     * Get field name
     *
     * @return string
     */
    public function getName(): string;    

    /**
     * Get field title
     *
     * @return string|null
    */
    public function getTitle(): ?string;

    /**
     * Get field type
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Set field value
     *
     * @param mixed $value
     * @return void
     */
    public function setValue($value): void;

    /**
     * Get field value
     *
     * @return mixed
     */
    public function getValue();
}
