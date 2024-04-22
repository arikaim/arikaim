'use strict';

arikaim.component.onLoaded(function(component) {  

    component.init = function() {
        var clearButton = $(component.getElement()).find('.clear-option-button');
        var saveButton = $(component.getElement()).find('.save-option-button');
        var field = $(component.getElement()).find('input');
      
        $(field).off();
        $(field).on('keydown',function() {          
            $(saveButton).removeClass('disabled');
        });

        $(clearButton).off();
        $(clearButton).on('click',function() {
            $(field).val('');
            $(saveButton).removeClass('disabled');
        });

        $(saveButton).off();
        $(saveButton).on('click',function() {
            options.save(component.get('option-name'),$(field).val(),function() {
                $(saveButton).addClass('disabled');
            });
        });
    }

    // init
    component.init();

    return component;
});