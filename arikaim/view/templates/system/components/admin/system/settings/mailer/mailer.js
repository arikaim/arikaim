/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function MailerSettings() {

    var self = this;
    var form_id = '#mailer_settings_form';
    var component = arikaim.getComponent('system:admin.system.settings.mailer');

    this.saveTransportType = function(option_name,value) {
        settings.save(option_name,value,function(result) {
        });
    };

    this.sendTestEmail = function() {
        settings.saveAll(form_id,function(result) {
            arikaim.get('/admin/api/mailer/test/email',function(result) {
                $('#send_button').removeClass('loading');
                $('#send_button').addClass('disabled');            
            });
        });
    };

    this.init = function() {
        $('#send_button').off();
        $('#send_button').on('click',function() {
            $(this).addClass('loading');
            self.sendTestEmail();
        });
        $('#use_sendmail').off();
        $('#use_sendmail').checkbox({
            onChecked: function() {
                var option_name = $(this).attr('name');
                self.saveTransportType(option_name,true);
                $(form_id).children().addClass('disabled'); 
                $('#test_email').hide();
                arikaim.form.clearErrors(form_id);
            },
            onUnchecked: function() {
                var option_name = $(this).attr('name');
                self.saveTransportType(option_name,false);
                $(form_id).children().removeClass('disabled'); 
                $('#test_email').show();
            }
        }); 
    
        var checked = $('#use_sendmail').checkbox('is checked');
        if (checked == true) {
            $(form_id).children().addClass('disabled');
            $('#test_email').hide();
        }
        arikaim.form.addRules(form_id,{
            inline: false,
            fields: {
                user_name: {
                    rules: [{ type: 'minLength[2]' }]          
                },        
                password: {
                    rules: [{ type: 'minLength[2]' }]   
                },
                port: {
                    rules: [{ type: 'minLength[2]' }]   
                },
                host: {
                    rules: [{ type: 'minLength[5]' }]   
                }
            }
        });
        arikaim.form.onSubmit(form_id,function() {
            self.save(form_id);
        });
    };

    this.save = function() {
        settings.saveAll(form_id,function(result) {
            var msg = component.getProperty('messages.save');  
            arikaim.form.showMessage({ msg: msg, auto_hide: 1000 });
            $(this).removeClass('disabled');        
        });
    };
}

var mailerSettings = new MailerSettings();

arikaim.page.onReady(function() {
    mailerSettings.init();
});