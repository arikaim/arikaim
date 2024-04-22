'use strict';

arikaim.component.onLoaded(function() {  
    $('.change-option').checkbox({
        onChecked: function() {         
            var optionName = $(this).attr('name');
            options.saveConfigOption(optionName,true);
            $('#logger_type_settings').show();
        },
        onUnchecked: function() {
            var optionName = $(this).attr('name');
            options.saveConfigOption(optionName,false);
            $('#logger_type_settings').hide();
        }
    });    
    
    $('.log-settings').checkbox({
        onChecked: function() {         
            var optionName = $(this).attr('name');
            options.saveConfigOption(optionName,true);           
        },
        onUnchecked: function() {
            var optionName = $(this).attr('name');
            options.saveConfigOption(optionName,false);          
        }
    });
});