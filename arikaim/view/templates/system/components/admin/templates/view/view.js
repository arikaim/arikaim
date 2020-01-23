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
    
        arikaim.ui.button('.set-primary',function(element) {  
            var name = $(element).attr('template');
                    
            return packages.setPrimary(name,'template',function(result) {
                var message = result.message;
                $('.primary-label').remove();
                $(this).addClass('disabled grey');
                $('.set-primary').removeClass('disabled grey');

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