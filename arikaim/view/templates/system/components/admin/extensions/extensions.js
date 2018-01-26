/**
 *  Arikaim
 *  Control Panel Extensions Component
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 *  
 */

/**
 *  @class  Extensions 
 *  Control panel extensions manager component
 */
function Extensions() {
    
    var self = this;

    this.showDetails = function(name) {
        $('.extension-tab').removeClass('active');
        $('#details_button').addClass('active');
        
        arikaim.page.loadContent({
            id: 'tab_content',
            component: 'system:admin.extensions.extension.settings',
            params: { extension_name: name }
        });   
    };
    
    this.install = function(name) {
        arikaim.put('/admin/api/extension/install/' + name,null,function(result) {           
            arikaim.page.loadContent({
                id: name,
                params: { extension_name: name },
                component: 'system:admin.extensions.extension',
                replace: true
            },function(result){
                extensions.init();
                // reload control panel menu
                menu.loadExtensionsMenu();
            });         
        });
    };
    
    this.unInstall = function(name) {
        arikaim.put('/admin/api/extension/uninstall/' + name,null,function(result) {
            arikaim.page.loadContent({
                id: name,
                params: { extension_name: name },
                component: 'system:admin.extensions.extension',
                replace: true
            },function(result){
                extensions.init();
                // reload control panel menu
                menu.loadExtensionsMenu();
            });
        });
    };

    this.changeStatus = function(name,status) {       
        if (isEmpty(status) == true) {
            var status = 'toggle';
        }
        if (status === true) status = 1;
        if (status === false) status = 0;

        arikaim.put('/admin/api/extension/status/' + name + "/" + status,null,function(result) {
            menu.loadExtensionsMenu();
        });
    };

    this.init = function() {
        $('.popup-button').popup({ 
            on: 'click' 
        });

        $('.details-button').off();
        $('.details-button').on('click',function() {     
            var name = $(this).attr('extension');        
            self.showDetails(name);
        });
        
        $('.install-button').off();
        $('.install-button').on('click',function() {             
            var name = $(this).attr('extension');
            self.install(name);        
        });
        
        $('.un-install-button').off();
        $('.un-install-button').on('click',function() {             
            var name = $(this).attr('extension');
            confirmDialog.show({
                description: "Uninstall extension \"" + name + "\""
            },function() {
                self.unInstall(name);
            });
        });
        
        $('.change-status-button').off();
        $('.change-status-button').on('click',function() {             
            var name = $(this).attr('extension');
            self.changeStatus(name);
        }); 
    };

    this.initTabs = function() {
        $('.extension-tab').off();
        $('.extension-tab').on('click',function() {              
            $('.extension-tab').removeClass('active');
            $(this).addClass('active');
            var component_name = $(this).attr('component');
            arikaim.page.loadContent({
                id: 'tab_content',
                component: component_name
            });   
        });
    }
}

var extensions = new Extensions();

arikaim.onPageReady(function() {   
    $('#extensions_tab .item').tab();
    extensions.initTabs();    
});
