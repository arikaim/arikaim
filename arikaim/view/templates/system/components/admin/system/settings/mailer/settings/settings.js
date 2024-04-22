'use strict';

arikaim.component.onLoaded(function() {    
    arikaim.ui.form.addRules('#mailer_settings_form');

    arikaim.ui.form.onSubmit('#mailer_settings_form',function(data) {         
        return options.saveAll('#mailer_settings_form');
    },function(result) {           
        arikaim.ui.form.showMessage(result.message);    
    },function(error) {
    });
});