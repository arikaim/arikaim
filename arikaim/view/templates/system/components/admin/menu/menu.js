/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ControlPanelMenu() {
    var self = this;
    
    this.init = function() {     
        $('.popup').popup();   
        $('#admin_menu_dropdown').dropdown();
    
        arikaim.ui.button('#system_menu_button',function(element) {   
            var status = $(element).attr('status');
            if (status == 'true' || status == true) {                
                $(element).attr('status',false);
                $('#system_menu').fadeOut(500);                   
                options.save('admin.menu.button',false);              
            } else {               
                $(element).attr('status',true);
                $('#system_menu').fadeIn(500);                  
                options.save('admin.menu.button',true);
            }           
        });
    
        arikaim.ui.button('.admin-menu-link',function(element) {
            $('.admin-menu-link').removeClass('active');     
            $(element).addClass('active');       
            controlPanel.setPageTitle($(element).attr('page-title'));
            controlPanel.setPageIcon($(element).attr('page-icon'));
            
            return arikaim.page.loadContent({
                id: 'tool_content',
                extension: $(element).attr('extension'),
                component: $(element).attr('component')
            });
        });
    };

    this.loadExtensionsMenu = function() {
        arikaim.page.loadContent({
            id: 'extensions_menu',           
            component: "system:admin.menu.extensions",
            params: { 
                type: 0,
                status: 1 
            }
        },function(result) {
            self.init();
        });
    };

    this.loadSystemMenu = function() {
        arikaim.page.loadContent({
            id: 'system_menu',           
            component: "system:admin.menu.system",
            params: { type: 1 }
        },function(result) {
            self.init();
        });
    };

    this.loadMenu = function() {
        arikaim.page.loadContent({
            id: 'admin_menu',           
            component: "system:admin.menu"
        },function(result) {
            self.init();
        });
    };
}

var menu = new ControlPanelMenu();

arikaim.component.onLoaded(function() {    
    menu.init();    
});
