/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ExtensionsView() {
    var self = this;

    this.init = function() {        
        this.loadMessages('system:admin.extensions');

        $('.popup-button').popup({ 
            on: 'click' 
        });

        $('.enable-dropdown').dropdown({
            onChange: function(value) {               
                var name = $(this).attr('extension');
                return packages.changeStatus(name,'extension',value,function(result) {
                    menu.loadExtensionsMenu();
                    menu.loadSystemMenu();
                    var message = result.message;

                    arikaim.page.loadContent({
                        id: name,
                        params: { extension_name: name },
                        component: 'system:admin.extensions.extension',
                        replace: true
                    },function(result) {  
                        self.init();    
                        arikaim.page.toastMessage(message);            
                    });
                },function(error) {
                    arikaim.page.toastMessage({
                        message: message,
                        class: 'error'
                    });                           
                });     
            }
        });

        arikaim.ui.button('.details-button',function(element) {
            var name = $(element).attr('extension');        
            extensions.showDetails(name);
        });
      
        arikaim.ui.button('.install-button',function(element) {
            var name = $(element).attr('extension');
         
            return packages.install(name,'extension',function(result) {
                var message = result.message;

                arikaim.page.loadContent({
                    id: name,
                    params: { extension_name: name },
                    component: 'system:admin.extensions.extension',
                    replace: true
                },function(result) {                                           
                    // reload control panel menu
                    self.init();
                  
                    arikaim.page.toastMessage(message);
                    menu.loadSystemMenu();
                    menu.loadExtensionsMenu();
                });               
            },function(error) {
                arikaim.page.toastMessage({
                    message: error,
                    class: 'error'
                });                 
            });        
        });
       
        arikaim.ui.button('.un-install-button',function(element) {             
            var name = $(element).attr('extension');          
            var message = arikaim.ui.template.render(self.getMessage('uninstall.description'),{ title: name });

            modal.confirm({
                title: self.getMessage('uninstall.title'),
                description: message
            }).done(function() {                              
                return packages.unInstall(name,'extension').done(function(result) {
                    arikaim.page.loadContent({
                        id: name,
                        params: { extension_name: name },
                        component: 'system:admin.extensions.extension',
                        replace: true
                    },function(result) {
                        // reload control panel menu
                        self.init();                       
                        menu.loadSystemMenu();
                        menu.loadExtensionsMenu();
                    });                  
                }).fail(function(error) {
                    arikaim.page.toastMessage({
                        message: error,
                        class: 'error'
                    }); 
                });
            });
        });

        arikaim.ui.button('.update-button',function(element) {
            var name = $(element).attr('extension');

            return packages.update(name,'extension').done(function(result) {
                var message = result.message;              
                arikaim.page.loadContent({
                    id: name,
                    params: { extension_name: name },
                    component: 'system:admin.extensions.extension',
                    replace: true
                },function(result) {                  
                    // reload control panel menu                  
                    self.init();
                    arikaim.page.toastMessage(message);

                    menu.loadSystemMenu();
                    menu.loadExtensionsMenu();
                });
            }).fail(function(error) {
                arikaim.page.toastMessage({
                    message: error,
                    class: 'error'
                }); 
            });
        });

        arikaim.ui.button('.set-primary',function(element) {  
            var name = $(element).attr('extension');
                    
            return packages.setPrimary(name,'extension',function(result) {
                var message = result.message;
                $('.primary-label').remove();
                $(this).addClass('disabled grey');
                $('.set-primary').removeClass('disabled grey');

                arikaim.page.loadContent({
                    id: name,
                    params: { extension_name: name },
                    component: 'system:admin.extensions.extension',
                    replace: true
                },function(result) {
                    self.init();
                    arikaim.page.toastMessage(message);                
                });  
            },function(error) {
                arikaim.page.toastMessage({
                    message: error,
                    class: 'error'
                }); 
            });
        });
    };
}

var extensionsView = createObject(ExtensionsView,ControlPanelView);

arikaim.component.onLoaded(function() {  
    extensionsView.init();
});
