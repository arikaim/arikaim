<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Db;

use Arikaim\Core\Collection\AbstractDescriptor;

/**
 * Table schema properties descriptior
 */
class TableSchemaDescriptor extends AbstractDescriptor
{
    /**
     * Define properties 
     *
     * @return void
     */
    protected function definition(): void
    {
        $this->property('id',function($property) {
            $property
                ->title('Id')
                ->type('number')   
                ->required(true);                           
        });
        $this->property('uuid',function($property) {
            $property
                ->title('Uuid')
                ->type('text')   
                ->required(true);                              
        });
    }
}
