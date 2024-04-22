'use strict';

arikaim.component.onLoaded(function() {  
    $('#templates_dropdown').dropdown({
        onChange: function(name) {
            arikaim.page.loadContent({
                id: 'template_details',
                component: "system:admin.templates.template.details.tabs",
                params: { template_name : name },
                useHeader: true
            },function(result) {
                $('#templates_details_tab .item').tab();
            });     
        }
    });   
});