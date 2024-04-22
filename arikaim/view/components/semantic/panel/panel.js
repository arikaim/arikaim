'use strict';

arikaim.component.onLoaded(function(component) {

    component.init = function() {
        var closeButtons = $(component.getElement()).find('.panel-close-button');
        arikaim.ui.button(closeButtons,function(element) { 
            $(component.getElement()).parent().hide();     
            $(component.getElement()).remove();             
        });
        $(component.getElement()).parent().show();
    };
    
    component.init();

    return component;
});