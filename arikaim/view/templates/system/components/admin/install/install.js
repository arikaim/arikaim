/**
 *  Arikaim
 *  Install component
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function Install() {

    var install_button_id  = "#install_button";
    var continue_button_id = "#continue_button";
    var self = this;
    this.status = null;

    var on_comlete = function() {  
        if (self.status == true) {
            // installed
            $(continue_button_id).show();
            $(install_button_id).hide();
            self.hideErrors();
        } else {
            // not yet installed or error 
            $(continue_button_id).hide();
            $(install_button_id).show();
        }
    };

    var on_before_comlete = function() {
        return self.status;
    }

    this.start = function() {
        try {
            arikaim.clearErrors();
            arikaim.setToken("");
            self.status = null;
            progressBar.reset();
            progressBar.show();
            self.disableInstallButton();

            progressBar.start({
                onComplete: on_comlete,
                onBeforeComplete: on_before_comlete
            });

            arikaim.post('/admin/api/install/','#config_form',function(result) {  
                // istallation completed
                self.status = true;
            },function(errors) {
                // error install
                progressBar.reset();
                progressBar.hide();
                $(continue_button_id).hide();
                self.enebleInstallButton();
                arikaim.page.showSystemErrors(errors,'#errors');
                self.status = false;
            },'session');
        } catch(error) {
            progressBar.reset();
            $(continue_button_id).hide();
            $(install_button_id).show();
        }
    };

    this.hideErrors = function() {    
        $('#errors').hide();
    };

    this.disableInstallButton = function() {
        $(install_button_id).addClass('disabled');
    };

    this.enebleInstallButton = function() {
        $(install_button_id).removeClass('disabled');
    };

    this.init = function() {
        progressBar.hide();
        this.hideErrors();
        // continue button
        $(continue_button_id).hide();
        $(continue_button_id).off();
        $(continue_button_id).on('click',function() {   
            arikaim.page.load(arikaim.getBaseUrl() + '/admin/'); // load admin panel page
        });
    
        // install button
        $(install_button_id).off();
        $(install_button_id).on('click',function() {  
            if (arikaim.form.validate('#config_form') == false) {          
                return false;
            } 
            install.start();
        });
        
    };
}

var install = new Install();

arikaim.page.onReady(function() {
    install.init();
});
