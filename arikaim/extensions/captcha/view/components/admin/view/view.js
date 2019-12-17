/**
 *  Arikaim
 *  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 * 
 *  Extension: Captcha
 *  Component: captcha::admin.view
*/

function CaptchaControlPanelView() {

    var self = this;

    this.init = function() {
        arikaim.events.on('driver.config',function(element,name,category) {
            arikaim.ui.setActiveTab('#settings_button');
            return drivers.loadConfig(name,'tab_content');           
        },'driverConfig');       
        
        arikaim.ui.button('.view-button',function(element) {
            var driver_name = $(element).attr('driver-name');         
            arikaim.ui.setActiveTab('#captcha_view_button');

            return self.previewCapcha(driver_name);
        });
    };

    this.previewCapcha = function(driver_name) {
        return arikaim.page.loadContent({
            id: 'tab_content',
            component: 'captcha::admin.preview',
            params: { driver_name: driver_name }
        });
    };
}

var captchaView = new CaptchaControlPanelView();

arikaim.page.onReady(function() {
    captchaView.init();
});