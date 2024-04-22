<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Content\Type;

use Arikaim\Core\Content\Type\ContentType;

/**
 * Array content type class
*/
class ArrayContentType extends ContentType 
{
    /**
     * Define email content type
     *
     * @return void
     */
    protected function define(): void
    {
        $this->setName('array');
        $this->setTitle('Array');      
    }

    /**
     * Create
     *
     * @return Self
     */
    public static function create()
    {
        return new ArrayContentType();
    }
}
