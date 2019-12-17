/**
 *  Arikaim
 *  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 * 
 */

 function TemplatesView() { 
    var self = this;

    this.init = function() {       
    
        arikaim.ui.button('.set-current-button',function(element) {  
            var name = $(element).attr('template');
                    
            return packages.setCurrent(name,'template',function(result) {
                var message = result.message;
                $('.current-template').remove();
                $(this).addClass('disabled grey').removeClass('olive');
                $('.set-current-button').removeClass('disabled grey').addClass('olive');

                arikaim.page.loadContent({
                    id: name,
                    params: { template_name: name },
                    component: 'system:admin.templates.template',
                    replace: true
                },function(result) {
                    self.init();
                    arikaim.ui.form.showMessage({
                        selector: '#message_' + name,
                        message: message
                    });
                });  
            },function(error) {
                arikaim.ui.form.showMessage({
                    selector: '#message_' + name,
                    message: error
                });
            });
        });
    
        arikaim.ui.button('.update-button',function(element) {  
            var name = $(element).attr('template');   
          
            return packages.update(name,'template',function(result) {
                var mesasge = result.message;
                arikaim.page.loadContent({
                    id: name,
                    params: { template_name: name },
                    component: 'system:admin.templates.template',
                    replace: true
                },function(result) {
                    self.init();   
                    arikaim.ui.form.showMessage({
                        selector: '#message_' + name,
                        message: mesasge
                    });                 
                });               
            },function(error) {
                arikaim.ui.form.showMessage({
                    selector: '#message_' + name,
                    message: error
                });
            });
        });
    
        arikaim.ui.button('.details-button',function(element) {  
            var name = $(element).attr('template');          
            templates.showDetailsPage(name);
            
            return true;
        });
    };
}

var templatesView = new TemplatesView();

arikaim.page.onReady(function() {    
    templatesView.init();
});