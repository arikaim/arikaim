'use strict';

arikaim.component.onLoaded(function(component) {
   
    component.init = function() {  
        $(this.getElement()).calendar({
            type: component.get('type'),
            onChange: function(date, text, type) {
                var value = (date === null) ? '' : Math.floor(date.getTime() / 1000);              
                $('#' + component.get('field')).val(value);
            }
        });
    };

    component.init();

    return component;
});