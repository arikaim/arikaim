/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
*/
'use strict';

function PasswordRecovery() { 
    var self = this;
  
    this.formInit = function() {
        arikaim.ui.form.addRules(formId,{
            inline: false,
            fields: {
                email: {
                    rules: [{ type: "email" }]
                }
            }
        });
    
        arikaim.ui.form.onSubmit('#password_recovery_form',function(data) {
            return self.send();
        }).done(function(result) {
            self.showDoneMessage();
        });
    };

    this.showDoneMessage = function() {
    };

    this.send = function(onSuccess, onError) {
        return arikaim.post('/core/api/user/password/recovery/',formId,onSuccess,onError);         
    };
}

var recovery = new PasswordRecovery();

