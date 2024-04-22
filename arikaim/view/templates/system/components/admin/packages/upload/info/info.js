'use strict';

arikaim.component.onLoaded(function() {    
   
    initButtons();

    function initButtons() {
        arikaim.ui.button('.confirm-upload-button',function(element) {  
            var name = $(element).attr('package-directory');

            return packages.confirmUpload(name,function(result) {               
                $('#package_info').hide();
                $('#upload_panel').hide();
                
                $('#message').show();
                $('#message').html(result.message);
                
            },function(error) {
                arikaim.page.toastMessage({
                    class: 'error',
                    message: error
                });
            });
        });
    
        arikaim.ui.button('.cancel-upload-button',function(element) {  
            return arikaim.page.loadContent({
                id: 'tab_content',           
                component: 'system:admin.packages.upload'
            },function(result) {
                initButtons();
            });
        });
    }
});