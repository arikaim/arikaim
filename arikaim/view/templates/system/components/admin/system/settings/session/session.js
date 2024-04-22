'use strict';

arikaim.component.onLoaded(function() {    
    arikaim.ui.form.addRules('#session_settings_form',{
        inline: false,
        fields: {
            recreate_interval: {
                identifier: 'recreation',
                rules: [
                    { type: 'empty' },
                    { type: 'integer[0..10000]'}
                ]          
            }
        }
    });

    arikaim.ui.form.onSubmit('#session_settings_form',function() {                     
        var interval = $('#interval').val();
        interval = getDefaultValue(interval,0);
        return options.saveConfigOption('settings/sessionInterval',interval);       
    },function(result) {
        arikaim.ui.form.showMessage(result.message);
    });
});
