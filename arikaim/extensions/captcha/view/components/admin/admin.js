/**
 *  Arikaim
 *  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 * 
 *  Extension: Captcha
 *  Component: captcha:admin
 */

function CaptchaControlPanel() {
   
    this.init = function() {    
        arikaim.ui.tab('.tab-item','tab_content',['category','driver_name']);
    };

    this.loadCaptcha = function(driver_name, element_id) {
        element_id = getDefaultValue(element_id,'tab_content');
        return arikaim.page.loadContent({
            id: element_id,
            component: 'captcha::code',
            params: { driver_name: driver_name }
        });
    };
}

var captcha = new CaptchaControlPanel();

arikaim.page.onReady(function() {
    captcha.init();
});