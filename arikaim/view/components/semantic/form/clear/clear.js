'use strict';

arikaim.component.onLoaded(function(component) {
    arikaim.ui.button('.clear-button',function(element) {
        var formId = $(element).attr('form-id');   
        if (isEmpty(formId) == false) {
            $('#' + formId).form('reset');
        } else {
            $('.form').form('reset');
        }         
    });
});