'use strict';

arikaim.component.onLoaded(function() {    

    arikaim.ui.button('.update-composer-package',function(element) {          
        var name = $(element).attr('package-name');

        return packages.install(name,'composer',function(result) {                    
            arikaim.page.toastMessage(result.message);
        },function(error) {              
            arikaim.page.toastMessage({
                selector: '#message_' + name,
                message: error,
                class: 'error',
                removeClass: 'success'
            });
        });
    });
});