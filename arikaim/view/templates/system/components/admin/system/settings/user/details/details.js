'use strict';

arikaim.component.onLoaded(function() {    
    arikaim.ui.form.addRules("#user_details_form");

    arikaim.ui.form.onSubmit('#user_details_form',function() {
        return settings.changeUserDetails('#user_details_form');
    },function(result) {       
        arikaim.ui.form.showMessage(result.message);       
    }); 
});