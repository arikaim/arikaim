/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
*/
'use strict';

function CaptchaControlPanelView() {
    var self = this;

    this.init = function() {
        arikaim.events.on('driver.config',function(element,name,category) {
            arikaim.ui.setActiveTab('#settings_button');
            return drivers.loadConfig(name,'tab_content');           
        },'driverConfig');       
        
        arikaim.ui.button('.view-button',function(element) {
            var driverName = $(element).attr('driver-name');         
            arikaim.ui.setActiveTab('#captcha_view_button');

            return self.previewCapcha(driverName);
        });
    };

    this.previewCapcha = function(driverName) {
        return arikaim.page.loadContent({
            id: 'tab_content',
            component: 'captcha::admin.preview',
            params: { driver_name: driverName }
        });
    };
}

var captchaView = new CaptchaControlPanelView();

arikaim.component.onLoaded(function() {
    captchaView.init();
});