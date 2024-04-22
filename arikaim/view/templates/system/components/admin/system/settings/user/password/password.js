'use strict';

arikaim.component.onLoaded(function() {    
    arikaim.ui.form.addRules("#user_password_form");

    arikaim.ui.form.onSubmit('#user_password_form',function() {
        return settings.changeUserPassword('#user_password_form');
    },function(result) {       
        arikaim.ui.form.showMessage(result.message);       
    }); 
});