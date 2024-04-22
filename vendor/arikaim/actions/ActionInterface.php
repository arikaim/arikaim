<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     Actions
*/
namespace Arikaim\Core\Actions;

/**
 * Action interface
 */
interface ActionInterface
{   
    /**
     * Run action
     *
     * @param mixed ...$params
     * @return mixed
     */
    public function run(...$params);

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Get result field
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, $default = null);

    /**
     * Return true if action is executed successful
     *
     * @return boolean
     */
    public function hasError(): bool;

    /**
     * Get execution error
     *
     * @return mixed
     */
    public function getError();

    /**
     * Set option
     *
     * @param string $name
     * @param mixed $value
     * @return ActionInterface
     */
    public function option(string $name, $value): ActionInterface;

    /**
     * Set options
     *
     * @param array $options
     * @return void
     */
    public function setOptions(array $options): void;
}
