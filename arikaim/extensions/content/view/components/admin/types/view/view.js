/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ContentTypesView() {
    var self = this;

    this.init = function() {     
    };

    this.initRows = function() {
        arikaim.ui.button('.content-type-details',function(element) {
            var name = $(element).attr('content-type');            
            self.loadDetails(name);
        });
    };

    this.loadDetails = function(name) {  
        return arikaim.page.loadContent({
            id: 'content_type_details',           
            component: 'content::admin.types.details',
            params: { content_type: name }            
        });  
    }
}

var contentTypesView = createObject(ContentTypesView,ControlPanelView);

arikaim.component.onLoaded(function() {
    contentTypesView.init();
    contentTypesView.initRows();
});