/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function ControlPanelMenu() {
    var self = this;
    
    this.init = function() {
        $('#system_menu_button').on('click',function() {         
            arikaim.page.showContent('system_menu','slide down');
        });
    
        $('.admin-menu-link').off();
        $('.admin-menu-link').on('click',function() {
            $('#admin_menu .item').removeClass('active');     
            $(this).addClass('active');       
            var component_name = $(this).attr('component');
            var extension_name = $(this).attr('extension');
            var id = $(this).attr('id');
            var icon = $(this).find(".menu-item-icon").attr('class');
            var title = $(this).find(".menu-item-title").html();
        
            controlPanel.setToolTitle(title);
            controlPanel.setToolIcon(icon);
            arikaim.page.loadContent({
                id: 'tool_content',
                extension: extension_name,
                component: component_name
            });
        });
    };

    this.loadExtensionsMenu = function() {
        arikaim.page.loadContent({
            id: 'extensions_menu',           
            component: "system:admin.menu.extensions"
        },function(result) {
            self.init();
        });
       
    };
}

var menu = new ControlPanelMenu();

arikaim.onPageReady(function() {
    menu.init();    
});