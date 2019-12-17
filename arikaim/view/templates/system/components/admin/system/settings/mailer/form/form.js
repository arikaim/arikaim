arikaim.page.onReady(function () {
    arikaim.ui.viewPasswordButton('.view-password','#password');
    $('#use_ssl').checkbox({});

    arikaim.ui.button('#send_button',function() {
        return mailerSettings.sendTestEmail();
    });

    $('#use_sendmail').checkbox({
        onChecked: function() {
            var optionName = $(this).attr('name');
            options.save(optionName,true);
            arikaim.ui.hide('#smtp_setings_form'); 
            mailerSettings.initSettingsForm(false);               
        },
        onUnchecked: function() {
            var optionName = $(this).attr('name');
            options.save(optionName,false);
            arikaim.ui.show('#smtp_setings_form');  
            mailerSettings.initSettingsForm(true);                     
        }
    }); 
  
    var useSmtp = $('#use_sendmail').checkbox('is checked');
    mailerSettings.initSettingsForm(!useSmtp);

    arikaim.ui.form.onSubmit('#mailer_settings_form',function(data) {         
        return options.saveAll('#mailer_settings_form');
    },function(result) {           
        arikaim.ui.form.showMessage(result.message);    
    },function(error) {

    });
});