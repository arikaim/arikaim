arikaim.page.onReady(function() {
    arikaim.ui.form.onSubmit('#user_settings_form',function() {
        return arikaim.post('/core/api/user/update','#user_settings_form');
    },function(result) {       
        arikaim.ui.form.showMessage(result.message);       
    }); 
});