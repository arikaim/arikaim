/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
*/

function PasswordRecovery() {
    
    var self = this;
    var formId = '#password_recovery_form';
    var component = arikaim.component.get('system:admin.password-recovery');

    this.init = function() {
        arikaim.ui.form.addRules(formId,{
            inline: false,
            fields: {
                email: {
                    rules: [{ type: "email" }]
                }
            }
        });
    
        arikaim.ui.form.onSubmit(formId,function(data) {
            return self.send();
        }).done(function(result) {
            self.showDoneMessage();
        });
    
        $('.open-login-page').on('click',function() {
            arikaim.page.loadContent({
                id : 'login_box',
                component: 'system:admin.login-form'
            });
        });
    };

    this.showDoneMessage = function() {
        var message = component.getProperty('messages.email');

        $('#revovery_button').removeClass('disabled loading');
        $('#revovery_button').hide();
        $('#password_recovery_form').hide();
        arikaim.page.show('#login_page_button');
        var email = $('#email').val()
        message += "<b>" + email + "</b>"; 
        
        arikaim.ui.form.showMessage(message);
    };

    this.send = function(onSuccess, onError) {
        return arikaim.post('/core/api/user/password/recovery/',formId,onSuccess,onError);         
    };
}

var recovery = new PasswordRecovery();

arikaim.page.onReady(function () {
    recovery.init();
});
