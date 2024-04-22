/**
 *  Arikaim  
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function Install() {
    var self = this;
    this.messages = null;

    this.prepare = function(formId, onSuccess, onError) {
        return arikaim.post('/core/api/install/prepare',formId,onSuccess,onError);
    };

    this.install = function(formId, onProgress, onSuccess, onError) {
        return taskProgress.post('/core/api/install/',formId,onProgress,onSuccess,onError);
    };

    this.extensions = function(onProgress, onSuccess, onError) {
        return taskProgress.put('/core/api/install/extensions',{},onProgress,onSuccess,onError);
    };

    this.modules = function(onProgress, onSuccess, onError) {
        return taskProgress.put('/core/api/install/modules',{},onProgress,onSuccess,onError);
    };

    this.actions = function(onProgress, onSuccess, onError) {
        return taskProgress.put('/core/api/install/actions',{},onProgress,onSuccess,onError);
    };

    this.repair = function(onSuccess, onError) {
        return arikaim.put('/core/api/install/repair',null,onSuccess,onError);
    };

    this.installModules = function(onSuccess, onError) {
        $('#install_progress').progress('reset');
     
        return this.modules(
            function(result) {
                $('#install_progress').progress('increment');
                $('#install_progress').progress('set label',result.message);
            },function(result) {            
                $('#install_progress').progress('complete',true);
                callFunction(onSuccess,result);
            },function(error) {
                callFunction(onError,error);
            }
        );
    };

    this.installCore = function(formId, onSuccess, onError) {
        $('#install_progress').progress('reset');
      
        return this.install(formId,
            function(result) {
                $('#install_progress').progress('increment');
                $('#install_progress').progress('set label',result.message);
            },function(result) {            
                $('#install_progress').progress('complete',true);
                callFunction(onSuccess,result);
            },function(error) {               
                callFunction(onError,error);
            }
        );
    };

    this.installExtensions = function(onSuccess, onError) {
        $('#install_progress').progress('reset');
      
        return this.extensions(
            function(result) {
                $('#install_progress').progress('increment');
                $('#install_progress').progress('set label',result.message);
            },function(result) {            
                $('#install_progress').progress('complete',true);
                callFunction(onSuccess,result);
            },function(error) {
                callFunction(onError,error);
            }
        );
    };

    this.postInstallActions = function(onSuccess, onError) {
        $('#install_progress').progress('reset');
      
        return this.actions(
            function(result) {
                $('#install_progress').progress('increment');
                $('#install_progress').progress('set label',result.message);
            },function(result) {            
                $('#install_progress').progress('complete',true);               
                callFunction(onSuccess,result);
            },function(error) {
                callFunction(onError,error);
            }
        );      
    };

    this.loadMessages = function() {
        if (isObject(this.messages) == true) {
            return;
        }

        arikaim.component.loadProperties('system:install.messages',function(params) { 
            self.messages = params.messages;
        }); 
    };

    this.initInstallForm = function() {
        arikaim.ui.form.addRules("#install_form",{
            inline: false,
            fields: {
                host: {
                    rules: [{ type: "minLength[2]" }]
                },
                database: {
                    rules: [{ type: "minLength[2]" }]
                },
                username: {
                    rules: [{ type: "minLength[2]" }]
                },
                password: {
                    rules: [{ type: "minLength[2]" }]
                }
            }
        });
    };

    this.init = function() {
        this.loadMessages();
        $('#continue_button').hide();      
    };

    this.showError = function(error) {
        $('#continue_button').hide();
        $('.install-button').show();
        error = (isArray(error) == true) ? error[0] : error;
     
        $('#install_progress').progress('set error',error);      
    }

    this.showComplete = function() {
        $('.install-button').hide();      
        arikaim.ui.form.disable('#install_form');
        $('#continue').removeClass('hidden');
        $('#continue').show();
        $('#continue_button').show();
        $('#progress_content').hide();

        arikaim.ui.form.showMessage({
            selector: '#message',
            hide: 0,
            message: self.messages.done
        });
    }
}

var install = new Install();

arikaim.component.onLoaded(function() {    
    install.init();
});
