'use strict';

arikaim.component.onLoaded(function() {    
    arikaim.ui.button('.init-storage',function(element) {
        return arikaim.put('/core/api/install/storage',null,function(result) {
            $('#message').show();
            console.log(result.message);

            arikaim.ui.form.showMessage({
                selector: '#message',               
                message: result.message
            });
        },function(error) {
            $('#message').show();
            arikaim.ui.form.showMessage({
                selector: '#message',  
                class: 'error',
                removeClass: 'success',             
                message: error
            });
        });
    });
});
