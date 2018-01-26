/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function MailerSettings() {

    var form_id = 'mailer_settings_form';
    var component = arikaim.getComponent('system:admin.settings.mailer-settings');

    this.init = function() {
        $('.change-option').off();
        $('.change-option').checkbox({
            onChecked: function() {
                var option_name = $(this).attr('name');
                settings.save(option_name,true);
                $('#' + form_id).children().addClass('disabled'); 
            },
            onUnchecked: function() {
                var option_name = $(this).attr('name');
                settings.save(option_name,false);
                $('#' + form_id).children().removeClass('disabled'); 
            }
        }); 
    
        var checked = $('.change-option').checkbox('is checked');
        if (checked == true) {
            $('#' + form_id).children().addClass('disabled'); 
        }

        arikaim.form.onSubmit(form_id,function() {
            settings.saveAll(form_id,function(result) {
                var msg = component.getProperty('messages.save');  
                arikaim.form.showMessage({ msg: msg, auto_hide: 1000 });
                $(this).removeClass('disabled');        
            });
        },function(errors) {
            arikaim.form.showErrors(errors,'.form-errors');
            $(this).removeClass('disabled');
        });
    };
}

var mailerSettings = new MailerSettings();

arikaim.onPageReady(function() {
    mailerSettings.init();
});