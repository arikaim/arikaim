/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
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
                use_header: true
            },function(result) {
                $('#templates_details_tab .item').tab();
            });     
        }
    });   
});