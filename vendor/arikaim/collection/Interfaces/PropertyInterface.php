<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Collection\Interfaces;

/**
 * Property interface
 */
interface PropertyInterface
{    
    const TEXT              = 0;
    const NUMBER            = 1;
    const CUSTOM            = 2;
    const BOOLEAN_TYPE      = 3;
    const LIST              = 4;
    const PHP_CLASS         = 5;
    const PASSWORD          = 6;
    const URL               = 7;
    const TEXT_AREA         = 8;
    const GROUP             = 9;
    const OAUTH             = 10;
    const LANGUAGE_DROPDOWN = 11;
    const IMAGE             = 12;
    const KEY               = 13;
    const PRICE             = 14;
    const FILE              = 15;
    const DATE              = 16;
    const TIME              = 17;
    const TIME_INTERVAL     = 18;

    /**
     * Get property name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get property id.
     *
     * @return string|null
     */
    public function getId(): ?string;
    
    /**
     * Get property value.
     *
     * @return mixed|null
     */
    public function getValue();

    /**
     * Get property display name.
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Get property description.
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Get property type.
     *
     * @return int|null
     */
    public function getType(): ?int;

    /**
     * Get property default value.
     *
     * @return mixed|null
     */
    public function getDefault();

    /**
     * Get property required attribute.
     *
     * @return boolean
     */
    public function getRequired(): bool;

    /**
     * Get property help
     *
     * @return string|null
    */
    public function getHelp(): ?string;

    /**
     * Get readonly attribute
     *
     * @return boolean
     */
    public function isReadonly(): bool;

    /**
     * Get hidden attribute
     *
     * @return boolean
    */
    public function isHidden(): bool;
}
