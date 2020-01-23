$(document).ready(function() {
    $('.accordion').accordion();
    
    arikaim.ui.form.addRules("#user_settings_form",{
        inline: false,
        fields: {
            user_name: ['minLength[2]']
        }
    });
});