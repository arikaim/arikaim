/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ModulesView() {
    var self = this;

    this.init = function() {
    };

    this.initRows = function() {
        arikaim.ui.button('.details-button',function(element) {    
            var name = $(element).attr('name');

            return arikaim.page.loadContent({
                id: 'tab_content',
                component: 'system:admin.modules.module.details',        
                params: { module: name }                    
            });
        });

        arikaim.ui.button('.install-button',function(element) {          
            var name = $(element).attr('name');

            return packages.install(name,'module',function(result) {   
                var message = result.message;            
                modules.loadModuleDetails(name,function(result) {
                    self.initRows();
                    arikaim.page.toastMessage(message);
                });               
            },function(error) {              
                self.initRows();               
                arikaim.page.toastMessage({
                    message: error,
                    class: 'error'
                }); 
            });
        });

        arikaim.ui.button('.update-button',function(element) {           
            var name = $(element).attr('name');

            return packages.update(name,'module',function(result) {
                var message = result.message;    
                modules.loadModuleDetails(name,function(result) {
                    self.initRows();
                    arikaim.page.toastMessage(message);
                }); 
            },function(error) {               
                self.initRows();
                arikaim.page.toastMessage({
                    message: error,
                    class: 'error'
                }); 
            });
        });

        arikaim.ui.button('.enable-button',function(element) {         
            var name = $(element).attr('name');

            return packages.enable(name,'module',function(result) {     
                var message = result.message;             
                modules.loadModuleDetails(name,function(result) {
                    self.initRows();
                    arikaim.page.toastMessage(message);
                });               
            },function(error) {              
                self.initRows();
                arikaim.page.toastMessage({
                    message: error,
                    class: 'error'
                }); 
            });
        });

        arikaim.ui.button('.disable-button',function(element) {          
            var name = $(element).attr('name');

            return packages.disable(name,'module',function(result) {   
                var message = result.message;               
                modules.loadModuleDetails(name,function(result) {
                    self.initRows();
                    arikaim.page.toastMessage(message);
                });               
            },function(error) {               
                self.initRows();
                arikaim.page.toastMessage({
                    message: error,
                    class: 'error'
                }); 
            });
        });
    };
}

var modulesView = new ModulesView();

arikaim.component.onLoaded(function() {    
    modulesView.initRows();
});
