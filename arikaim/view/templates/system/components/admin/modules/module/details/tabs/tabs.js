'use strict';

arikaim.component.onLoaded(function() {  
    $('#module_details_tab .item').tab();

    arikaim.ui.button('.update-composer',function(element) {          
        var name = $(element).attr('name');
     
        return packages.updateComposerPackages(name,'module',function(result) {
            var message = result.message;  
            arikaim.ui.form.showMessage({
                selector: '#message_' + name,
                message: message
            });    
           
            arikaim.page.loadContent({
                id: 'module_details',
                component: "system:admin.modules.module.details.tabs",
                params: { module_name : name },
               
            });  
        },function(error) { 
            arikaim.ui.form.showMessage({
                selector: '#message_' + name,
                message: error,
                class: 'error',
                removeClass: 'success'
            }); 
        });
    });
});