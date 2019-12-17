/**
 *  Arikaim
 *  http://www.arikaim.com
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 */

arikaim.page.onReady(function() {
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
