arikaim.page.onReady(function () {
    arikaim.ui.viewPasswordButton('.view-password','#password');
    
    arikaim.ui.form.addRules("#login_form",{
        inline: false,
        fields: {
            password: {
                rules: [{ type: "empty" }]
            },
            user_name: {
                rules: [{ type: "empty" }]
            }
        }
    });
    
    $('#forgotten_button').on('click',function() {
        arikaim.page.loadContent({
            id : 'login_box',
            component: 'system:admin.password-recovery'
        });
    });
    
    arikaim.ui.form.onSubmit('#login_form',function() {
        return user.login('#login_form',function(result) {              
            arikaim.ui.form.disable('#login_form');
            arikaim.ui.disableButton('.login-button');
            arikaim.page.reload();   
        });
    });
});