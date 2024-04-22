'use strict';

arikaim.component.onLoaded(function() {  
    $('.components-list').accordion({
        onOpen: function(item) {
            var componentName = $(this).attr('component-name');
            var elementId = $(this).attr('id');
            var extension = getDefaultValue($(this).attr('extension'),'');
            var template = getDefaultValue($(this).attr('template'),'');

            arikaim.page.loadContent({
                id: elementId,
                component: 'system:admin.component.details',
                params: { 
                    component: componentName,
                    extension: extension,
                    template: template  
                }
            });
        },
        onOpening: function(item) {
            var elementId = $(this).attr('id');
            $('#' + elementId).html("");
        }
    });
});
