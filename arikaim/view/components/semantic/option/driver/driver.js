'use strict';

arikaim.component.onLoaded(function(component) {
    $('#drivers_dropdown').dropdown({
        onChange: function(value) {  
            var optionName = $('#driver_field').attr('option-name');                  
            options.save(optionName,value);
        }
    });
});
