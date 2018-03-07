/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.page.onReady(function() {
    $('.accordion').accordion();
    var component = arikaim.getComponent('system:admin.system.settings.user');

    arikaim.form.addRules("#user_settings_form",{
        inline: false,
        fields: {
            user_name: ['minLength[2]']
        }
    });

    arikaim.form.onSubmit('#user_settings_form',function() {
        
        $('#save-button').addClass('loading');
        controlPanelUser.changeDetails('#user_settings_form',function(result) {
            var msg = component.getProperty('messages.save');  
            $('#save-button').removeClass('loading');
            arikaim.form.showMessage({ msg: msg, auto_hide: 1000 });
        },
        function(errors) {
            $('#save-button').removeClass('loading');
            arikaim.form.showErrors(errors,'.form-errors');
            arikaim.form.addFieldErrors('#user_settings_form',errors);
        });
    });
});