/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function System() {

    var system_tab_element = "system_tab";

    this.init = function() {   
        arikaim.page.loadContent({
            id: system_tab_element,
            component: 'system:admin.system.settings'
        });

        $('.system-link').off();
        $('.system-link').on('click', function() {
            $('.system-link').removeClass('active');
            $(this).addClass('active');       
            var component_name = $(this).attr('component');    
            if (isEmpty(component_name) == false) {   
                arikaim.page.loadContent({
                    id: system_tab_element,
                    component: component_name
                });       
            }
        });
    };
}

var system = new System();

arikaim.onPageReady(function() {
    system.init();    
});
