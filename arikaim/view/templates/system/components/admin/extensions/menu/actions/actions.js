$(document).ready(function() {  
    $('#extension_actions_dropdown').dropdown({
        onChange: function(value) {
            var extension = $(this).attr('extension');
            arikaim.ui.setActiveTab('#menu_tab');
            return arikaim.page.loadContent({
                id: 'tab_content',
                component: 'system:admin.extensions.extension.details',
                params: { extension: extension }
            });   
        }
    });
})