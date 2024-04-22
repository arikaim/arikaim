/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function LibrariesView() {
    var self = this;

    this.initRows = function() {
        arikaim.ui.button('.details-button',function(element) {
            var name = $(element).attr('library');      
            arikaim.ui.setActiveTab('#details_button');
    
            libraries.showDetails(name);
        });
    
        arikaim.ui.button('.status-button',function(element) {
            var name = $(element).attr('library');  
            var status = $(element).attr('status');     
         
            libraries.setStatus(name,status,function(result) {
                arikaim.page.loadContent({
                    id: 'library_' + name,
                    component: 'system:admin.libraries.library',
                    params: { library_name: name }
                },function(result) {           
                    self.initRows();
                }); 
            });
        });
    }
}

var librariesView = createObject(LibrariesView,ControlPanelView);

arikaim.component.onLoaded(function() {  
    librariesView.initRows();
});
