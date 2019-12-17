$(document).ready(function() {
    install.init();
    install.initInstallForm();

    arikaim.ui.form.onSubmit('#install_form',function(element) {              
        progressBar.start({
            onComplete: function() {  
                if (install.status == true) {
                    // installed
                    $('#continue_button').show();
                    $('.submit-button').hide();     
                    progressBar.hide(true);               
                } else {
                    // not yet installed or error 
                    $('#continue_button').hide();
                    $('.submit-button').show();
                }
            }
        });
        return install.install('#install_form');
        
    },function(result) {
        progressBar.reset();
        progressBar.hide(true);
        $('.submit-button').hide();      
        arikaim.ui.form.showMessage({
            selector: '#message',
            hide: 0,
            message: result.message
        });
        arikaim.ui.form.disable('#install_form');
        $('#continue_button').show();
        install.status = true;
    },function(error) {
        progressBar.reset();
        progressBar.hide(true);
        $('#continue_button').hide();
        $('.submit-button').show();
        install.status = false;
    });
});  