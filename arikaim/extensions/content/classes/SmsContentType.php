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
 * Sms content type class
*/
class SmsContentType extends ContentType 
{
    /**
     * Define text content type
     *
     * @return void
     */
    protected function define(): void
    {
        $this->setName('sms');
        $this->setTitle('Sms message');
        // fields      
        $this->addField('message','text.area','Message');     
        $this->addField('phone','text','Phone');      
    }
}
