/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.onPageReady(function() {
    $('.accordion').accordion();
    var component = arikaim.getComponent('system:admin.settings.user-settings');

    arikaim.form.addRules("user_settings_form",{
        inline: false,
        fields: {
            user_name: {
                identifier: "user_name", 
                rules: [{
                    type:'minLength[2]'
                }]
          }
        }
    });

    arikaim.form.onSubmit('user_settings_form',function() {
        $(this).addClass('disabled');
        controlPanelUser.changeDetails('user_settings_form',function(result) {
            var msg = component.getProperty('messages.save');  
            arikaim.form.showMessage({ msg: msg, auto_hide: 1000 });
            $(this).removeClass('disabled');
        },
        function(errors) {
            arikaim.form.showErrors(errors,'.form-errors');
            $(this).removeClass('disabled');
        });
    });
});
