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

use Arikaim\Core\Collection\AbstractDescriptor;

/**
 * Action properties descriptior
 */
class ActionPropertiesDescriptor extends AbstractDescriptor
{
    /**
     * Define properties 
     *
     * @return void
     */
    protected function definition(): void
    {
        $this->createCollection('options');
        $this->createCollection('result');
    }
}
