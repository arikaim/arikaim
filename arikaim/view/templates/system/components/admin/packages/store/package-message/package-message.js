'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.button('.open-package-download',function(element) {
        var contentId = $(element).attr('content-id');
        var type = $(element).attr('package-type');
        
        return arikaim.page.loadContent({
            id: contentId,           
            component: 'system:admin.packages.store.view',
            params: { 
                type: type                
            }
        },function(result) {                   
        });
    });
});