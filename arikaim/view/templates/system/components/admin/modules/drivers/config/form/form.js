'use strict';

arikaim.component.onLoaded(function() {    
    arikaim.ui.form.addRules('#driver_config_form',{});

    arikaim.ui.form.onSubmit('#driver_config_form',function() {
        return drivers.saveConfig('#driver_config_form');
    },function(result) {         
        arikaim.ui.form.showMessage(result.message);           
    });
});