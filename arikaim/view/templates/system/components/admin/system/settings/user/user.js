arikaim.page.onReady(function() {
    $('.accordion').accordion();
    
    arikaim.ui.form.addRules("#user_settings_form",{
        inline: false,
        fields: {
            user_name: ['minLength[2]']
        }
    });

    arikaim.ui.form.onSubmit('#user_settings_form',function() {
        return arikaim.post('/core/api/user/','#user_settings_form');
    },function(result) {       
        arikaim.ui.form.showMessage(result.message);       
    }); 
});