/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function CaptchaControlPanel() {
   
    this.init = function() {    
        arikaim.ui.tab('.tab-item','tab_content',['category','driver_name']);
    };

    this.loadCaptcha = function(driverName, elementId) {
        elementId = getDefaultValue(elementId,'tab_content');
        return arikaim.page.loadContent({
            id: elementId,
            component: 'captcha::code',
            params: { driver_name: driverName }
        });
    };
}

var captcha = new CaptchaControlPanel();

arikaim.component.onLoaded(function() {
    captcha.init();
});