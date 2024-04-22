'use strict';

arikaim.component.onLoaded(function(component) {
    //   drivers.reloadConfig();
    arikaim.ui.button('.edit-key-button',function(element) {
        var fieldName = $(element).attr('field');
        var group = $(element).attr('group');
        var previewValue = $(element).attr('preview-value');

        arikaim.ui.loadComponent({
            name: 'semantic~properties.items.key.edit',
            mountTo: 'key_field_' + group + '_' + fieldName,
            params: {
                field_name: fieldName,
                group: group,
                preview: previewValue
            }
        })
    });
});