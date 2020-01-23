/**
 *  Arikaim  
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Install() {
    var self = this;

    this.install = function(formId, onSuccess, onError) {
        return arikaim.post('/core/api/install',formId,onSuccess,onError);
    };

    this.repair = function(onSuccess, onError) {
        return arikaim.put('/core/api/install/repair',null,onSuccess,onError);
    };

    this.initInstallForm = function() {
        arikaim.ui.form.addRules("#install_form",{
            inline: false,
            fields: {
                database: {
                    rules: [{ type: "minLength[3]" }]
                },
                username: {
                    rules: [{ type: "minLength[3]" }]
                },
                password: {
                    rules: [{ type: "minLength[3]" }]
                }
            }
        });
    };

    this.init = function() {
        progressBar.hide(true);
        $('#continue_button').hide();      
        
        arikaim.ui.button('#continue_button',function() {
            return arikaim.loadUrl('/admin',true); 
        });
    };
}

var install = new Install();