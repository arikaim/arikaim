'use strict';

arikaim.component.onLoaded(function() {    
    $('#install_page_toggle').checkbox({
        onChecked: function() {
            settings.disableInstallPage(true);         
        },
        onUnchecked: function() {
            settings.disableInstallPage(false);         
        }
    }); 

    arikaim.ui.button('.repair-install',function(element) {
        return install.repair(function(result) {           
            arikaim.ui.form.showMessage({
                selector: '#message',               
                message: result.message
            });
        },function(error) {
            arikaim.ui.form.showMessage({
                selector: '#message',  
                class: 'error',
                removeClass: 'success',             
                message: error
            });
        });
    });
});
