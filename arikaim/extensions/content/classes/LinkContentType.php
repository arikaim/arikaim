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
 * Link content type class
*/
class LinkContentType extends ContentType 
{
    /**
     * Define link content type
     *
     * @return void
     */
    protected function define(): void
    {
        $this->setName('link');
        $this->setTitle('Link');
        // fields
        $this->addField('title','text','Title');
        $this->addField('url','text.area','Url');
        $this->addField('target','text','Target');
        $this->addField('options','list','Link Options');      
    }
}
