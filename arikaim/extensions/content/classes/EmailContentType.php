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
 * Email content type class
*/
class EmailContentType extends ContentType 
{
    /**
     * Define email content type
     *
     * @return void
     */
    protected function define(): void
    {
        $this->setName('email');
        $this->setTitle('Email');
        // fields
        $this->addField('to','email','Send To');
        $this->addField('from','email','From');
        $this->addField('subject','text','Subject');
        $this->addField('body','text.area','Text');      
    }
}
