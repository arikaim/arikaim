/**
 *  Arikaim
 *  http://www.arikaim.com
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  
 */
'use strict';

function Templates() { 
    var self = this;
   
    this.showDetailsPage = function(name) {
        arikaim.page.loadContent({
            id : 'tab_content',
            component : 'system:admin.templates.template.details',
            params: { 'template_name': name }
        },function(result) {
            $('#templates_details_tab .item').tab();
            arikaim.ui.setActiveTab('#details_button');
        });
    };

    this.init = function() {     
       arikaim.ui.tab();
    };
}

var templates = new Templates();

arikaim.component.onLoaded(function() {  
    templates.init();   
});