/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ContentProvidersView() {
    var self = this;

    this.init = function() {     
    };

    this.initRows = function() {
        arikaim.ui.button('.provider-details',function(element) {
            var name = $(element).attr('content-povider');            
            self.loadDetails(name);
        });
    };

    this.loadDetails = function(name) {    
        return arikaim.page.loadContent({
            id: 'provider_details',           
            component: 'content::admin.providers.details',
            params: { provider_name: name }            
        });  
    }
}

var contentProvidersView = createObject(ContentProvidersView,ControlPanelView);

arikaim.component.onLoaded(function() {
    contentProvidersView.init();
    contentProvidersView.initRows();
});