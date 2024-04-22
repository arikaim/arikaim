'use strict';

arikaim.component.onLoaded(function() {    
    $('.checkbox').checkbox({
        onChecked: function() {
            $(this).closest('.checkbox').find('.option-field').val(1);          
        },
        onUnchecked: function() {
            $(this).closest('.checkbox').find('.option-field').val(0);   
        }
    });
});