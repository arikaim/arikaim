'use strict';

arikaim.component.onLoaded(function() {  
    arikaim.ui.viewPasswordButton('.view-password','#password');
    arikaim.ui.form.addRules("#login_form");
    
    $('#forgotten_button').on('click',function() {
        arikaim.page.loadContent({
            id : 'login_box',
            component: 'system:admin.password-recovery'
        });
    });
    
    arikaim.ui.form.onSubmit('#login_form',function() {
        arikaim.ui.form.disable('#login_form');
        arikaim.ui.disableButton('.login-button');
        
        return user.login('#login_form',function(result) {   
            arikaim.ui.form.disable('#login_form');                 
            arikaim.page.reload();   
        },function(error) {
            arikaim.ui.form.enable('#login_form');
            arikaim.ui.enableButton('.login-button');
        });
    });
});