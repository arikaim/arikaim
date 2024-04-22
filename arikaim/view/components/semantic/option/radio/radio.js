'use strict';

arikaim.component.onLoaded(function(component) {
    $('.option-checkbox').checkbox({
        onChecked: function() {   
            var name = $(this).attr('name');   
            var value = $(this).val();
            options.save(name,value);          
        }      
    });
});