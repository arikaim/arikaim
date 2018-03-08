/**
 *  Arikaim
 *  http://www.arikaim.com
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  
 */

function Templates() {
    
    var self = this;
    var tab_item = '.template-tab';
    var current_button = '.set-current-button';
    var details_button = '.details-button';

    this.showDetailsPage = function(name) {
        arikaim.page.loadContent({
            id : 'tab_content',
            component : 'system:admin.templates.template.details',
            params: { 'template_name': name }
        },function(result) {
            $('#templates_details_tab .item').tab();
            $(tab_item).removeClass('active');
            $('#details_button').addClass('active');
        });
    };

    this.init = function() {
        $('.popup-button').popup({
            on: 'click'
        });
    
        $(current_button).off();    
        $(current_button).on('click',function() {  
            var name = $(this).attr('template');          
            self.setCurrent(name);
        });

        $(tab_item).off();
        $(tab_item).on('click',function() {              
            $(tab_item).removeClass('active');
            $(this).addClass('active');
            var component_name = $(this).attr('component');
            arikaim.page.loadContent({
                id: 'tab_content',
                component: component_name
            });   
        });
        $(details_button).off();
        $(details_button).on('click',function() {  
            var name = $(this).attr('template');          
            self.showDetailsPage(name);
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
}

var templates = new Templates();

arikaim.page.onReady(function() {
    templates.init();   
});