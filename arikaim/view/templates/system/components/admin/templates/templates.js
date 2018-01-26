/**
 *  Arikaim
 *  http://www.arikaim.com
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  
 */

function Templates() {
    
    this.showDetails = function(name) {
        /*
        arikaim.page.loadContent({
            id : 'template_details_' + name,
            component : 'system:admin.templates.template-details',
            params: { 'template_name': name }
        },function(result) {
            $('#templates_details_tab .item').tab();
        });
        */
    };
    
    this.init = function() {
        $('.popup-button').popup({
            on: 'click'
        });
    
        $('.set-current-button').off();    
        $('.set-current-button').on('click',function() {  
            var name = $(this).attr('template');          
            templates.setCurrent(name);
        });
    };

    this.setCurrent = function(name) {
        arikaim.put('/admin/api/template/current/' + name,null,function(result) {
            arikaim.page.loadContent({
                id: 'tool_content',
                component: 'system:admin.templates'
            });
        });
    };

    this.hideDetails = function(name) {
       // arikaim.page.hideContent('template_details_' + name,'slide down');
    };
}

var templates = new Templates();

arikaim.onPageReady(function() {
    templates.init();   
});