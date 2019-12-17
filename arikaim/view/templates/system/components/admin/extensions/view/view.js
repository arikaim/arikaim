/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function ExtensionsView() {
    var self = this;

    this.init = function() {
        var component = arikaim.component.get('system:admin.extensions');      
        var messageTitle = component.getProperty('message.title');

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
                    menu.loadSystemMenu();
                    menu.loadExtensionsMenu();
                    arikaim.ui.form.showMessage({
                        selector: '#message_' + name,
                        message: message
                    });
                });               
            },function(error) {
                arikaim.ui.form.showMessage({
                    selector: '#message_' + name,
                    message: error,
                    class: 'error',
                    removeClass: 'success'
                });
            });        
        });
       
        arikaim.ui.button('.un-install-button',function(element) {             
            var name = $(element).attr('extension');
            var message = component.getProperty('message.description');
            message = arikaim.ui.template.render(message,{ title: name });

            modal.confirm({
                title: messageTitle,
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
                    console.log(error);
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
                    menu.loadSystemMenu();
                    menu.loadExtensionsMenu();
                    arikaim.ui.form.showMessage({
                        selector: '#message_' + name,
                        message: message
                    });
                });
            }).fail(function(error) {
                arikaim.ui.form.showMessage({
                    selector: '#message_' + name,
                    message: error
                });
            });
        });
    };
}

var extensionsView = new ExtensionsView();

arikaim.page.onReady(function() {   
    extensionsView.init();
});
