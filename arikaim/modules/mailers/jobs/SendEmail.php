<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Mailers\Jobs;

use Arikaim\Core\Queue\Jobs\Job;
use Arikaim\Core\Arikaim;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Interfaces\Job\JobInterface;
use Arikaim\Core\Interfaces\ConfigPropertiesInterface;
use Arikaim\Core\Collection\Properties;
use Arikaim\Core\Collection\Traits\ConfigProperties;

/**
 * Send email
 */
class SendEmail extends Job implements JobInterface, ConfigPropertiesInterface
{
    use 
        ConfigProperties;

    /**
     * Run job
     *
     * @return mixed
     */
    public function execute()
    {      
        $config = $this->getConfigProperties();   

        $componentName = $config->getValue('component_name');
        $to = $config->getValue('to');
        $from = $config->getValue('from',null);
        $selector = $config->getValue('content_selector',null);

        if (empty($from) == true) {
            $user = Model::Users()->getControlPanelUser();
            $from = $user->email;
        }

        if (empty($componentName) == true || empty($to) == true) {
            return false;
        }
        // get content
        $content = Arikaim::content()->get($selector);
        $params = $content->getDataArray() ?? [];

        $result = Arikaim::mailer()->create($componentName,$params)                   
            ->to($to)
            ->from($from)
            ->send();
          
        return $result;    
    }

    /**
     * Init config properties
     *
     * @param Properties $properties
     * @return void
     */
    public function initConfigProperties(Properties $properties): void
    {
        $properties->property('to',function($property) {
            $property
                ->title('Send To')
                ->description('Recepient email address')
                ->type('text')
                ->required(true)
                ->readonly(false)            
                ->default('');
        });  
        $properties->property('component_name',function($property) {
            $property
                ->title('Email Component')
                ->description('Email component name')
                ->type('text')
                ->required(true)
                ->readonly(false)            
                ->default('');
        }); 
        $properties->property('from',function($property) {
            $property
                ->title('From')
                ->description('From email address')
                ->type('text')
                ->required(false)
                ->readonly(false)            
                ->default('');
        }); 
        $properties->property('content_selector',function($property) {
            $property
                ->title('Content Selector')
                ->description('Selector for email template variables.')
                ->type('text')
                ->required(false)
                ->readonly(false)            
                ->default('');
        }); 
    }
}
