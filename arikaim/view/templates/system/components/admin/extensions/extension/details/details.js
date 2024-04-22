'use strict';

arikaim.component.onLoaded(function() {  
    $('#extensions_dropdown').dropdown({
        onChange: function(name) {              
            arikaim.page.loadContent({
                id: 'extension_details',
                component: "system:admin.extensions.extension.details.tabs",
                params: { extension_name : name },
                useHeader: true
            });     
        }
    });   
});