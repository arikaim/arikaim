'use strict';

arikaim.component.onLoaded(function() {    
    $('#drivers_dropdown').dropdown({
        onChange: function(value) {             
            drivers.loadConfigForm(value,'driver_config');
        }
    });
});