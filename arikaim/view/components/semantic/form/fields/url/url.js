'use stric';

arikaim.component.onLoaded(function(component) {
    $('.http-schema-dropdown').dropdown({});

    arikaim.ui.button('.clear-url',function(element) {
        var fieldId = $(element).attr('url-field');
        $('#' + fieldId).val('');
        $('#' + fieldId).focus();
    });
});