/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.onPageReady(function() {
    arikaim.loadComponent('system:admin.menu',function(result) {
        arikaim.page.setContent('admin_menu',result.html,'scale');
         // toggle menu button
         $('.hide-menu-button').off();
         $('.hide-menu-button').on('click',function() { 
            
         });
    },function(errors) {

    });
});