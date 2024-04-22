<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Image\Controllers;

use Arikaim\Core\Controllers\ControlPanelApiController;

use Arikaim\Core\Controllers\Traits\Crud;

/**
 * Image collections contorl panel api controller
*/
class CollectionsControlPanel extends ControlPanelApiController
{
    use    
        Crud;

    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('image::admin.collections.messages');
        $this->setExtensionName('image');
        $this->setModelClass('ImageCollections');
        $this->onBeforeCreate(function($data, $model) {
            $data['user_id'] = $this->getUserId();
            return $data;
        });
    }
}
