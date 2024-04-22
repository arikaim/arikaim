'use strict';

arikaim.component.onLoaded(function() {    
    $('#logger_type_dropdown').dropdown({
        onChange: function(value, text, choice) {                 
            options.saveConfigOption('settings/loggerHandler',value);
        }
    });
});