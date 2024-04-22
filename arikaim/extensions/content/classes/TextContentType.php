<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Content\Classes;

use Arikaim\Core\Content\Type\ContentType;

/**
 * Text content type class
*/
class TextContentType extends ContentType 
{
    /**
     * Define text content type
     *
     * @return void
     */
    protected function define(): void
    {
        $this->setName('text');
        $this->setTitle('Text');
        // fields      
        $this->addField('text','text.area','Text');       
        $this->addField('title','text','Title');       
    }
}
