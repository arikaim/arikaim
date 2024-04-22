/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function Popup() { 
    var self = this;
    this.component = null;
    this.params = null;
    this.contentId = 'popup_content';

    this.init = function(selector, component, params, contentId) {
        this.contentId = getDefaultValue(contentId,'popup_content');    
        this.component = component;
        this.params = getDefaultValue(params, null);
        
        $(selector).popup({
            popup : $('.popup'),
            on: 'click',
            onVisible: function(popup) {
                var params = (isEmpty(self.params) == true) ? getElementAttributes(popup,['id','class']) : self.params;
                return arikaim.page.loadContent({
                    id: self.contentId,
                    params: params,
                    component: self.component
                },function(result) {                  
                });
            }
        });
    };
}

var popup = new Popup();