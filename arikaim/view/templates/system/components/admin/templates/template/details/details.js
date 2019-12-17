/**
 *  Arikaim
 *  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 * 
 */

arikaim.page.onReady(function() {
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