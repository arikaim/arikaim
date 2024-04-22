'use strict';

arikaim.component.onLoaded(function(component) {
    arikaim.ui.button('.cancel-edit-key-button',function(element) {
        var fieldName = $(element).attr('field');
        var group = $(element).attr('group');
        var value = $(element).attr('preview-value');

        arikaim.ui.loadComponent({
            name: 'semantic~properties.items.key',
            mountTo: 'key_field_' + group + '_' + fieldName,
            params: {
                field_name: fieldName,
                group: group,
                value: value
            }
        })
    });
});