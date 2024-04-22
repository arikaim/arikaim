'use strict';

arikaim.component.onLoaded(function() {    
    $('#time_zone').dropdown({
        onChange: function(value, text, choice) {   
            options.saveConfigOption('settings/timeZone',value);           
        }
    });

    $('#date_format').dropdown({
        onChange: function(value) {    
            options.saveConfigOption('settings/dateFormat',value);                         
        }
    });

    $('#time_format').dropdown({
        onChange: function(value) {         
            options.saveConfigOption('settings/timeFormat',value);                
        }
    });
});