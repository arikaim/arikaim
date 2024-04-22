<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Queue\Jobs;

use Arikaim\Core\Collection\AbstractDescriptor;

/**
 * Job properties descriptior
 */
class JobPropertiesDescriptor extends AbstractDescriptor
{
    /**
     * Define properties 
     *
     * @return void
     */
    protected function definition(): void
    {
        $this->property('title',function($property) {
            $property
                ->title('Title')
                ->type('text')   
                ->required(false)                    
                ->value('');                         
        });

        $this->property('description',function($property) {
            $property
                ->title('Description')
                ->type('text')   
                ->required(false)                    
                ->value('');                         
        });

        $this->property('allow.admin.push',function($property) {
            $property
                ->title('Allow push job from control panel')
                ->type('boolean')   
                ->required(true) 
                ->default(false)                   
                ->value(false);                         
        });

        $this->property('allow.console.push',function($property) {
            $property
                ->title('Allow push job from console')
                ->type('boolean')   
                ->required(true) 
                ->default(false)                   
                ->value(false);                         
        });

        $this->property('allow.console.run',function($property) {
            $property
                ->title('Allow run job from console')
                ->type('boolean')   
                ->required(true)  
                ->default(true)                        
                ->value(true);                         
        });

        $this->property('allow.admin.run',function($property) {
            $property
                ->title('Allow run job from control panel')
                ->type('boolean')   
                ->required(true) 
                ->default(false)                   
                ->value(false);                         
        });

        $this->property('allow.admin.config',function($property) {
            $property
                ->title('Allow config job from control panel')
                ->type('boolean')   
                ->required(true) 
                ->default(false)                   
                ->value(false);                         
        });

        $this->property('allow.console.config',function($property) {
            $property
                ->title('Allow config job from console')
                ->type('boolean')   
                ->required(true) 
                ->default(true)                   
                ->value(true);                         
        });

        $this->createCollection('parameters');
        $this->createCollection('result');
    }
}
