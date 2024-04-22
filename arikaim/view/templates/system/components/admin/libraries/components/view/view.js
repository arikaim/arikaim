/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ComponentsLibraryView() {
    this.initRows = function() {
        arikaim.ui.button('.components-details',function(element) {
            var name = $(element).attr('library-name');
            componentsLibraryView.loadDetails(name);
        });
    }

    this.loadDetails = function(name) {
        arikaim.ui.setActiveTab('#components_details','.components-tab-item');

        return arikaim.page.loadContent({
            id: 'components_content',
            component: "system:admin.libraries.components.info",
            params: { library_name : name }              
        });    
    }
}

var componentsLibraryView = createObject(ComponentsLibraryView,ControlPanelView);

arikaim.component.onLoaded(function() {  
    componentsLibraryView.initRows();
});
