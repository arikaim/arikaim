/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function MobileMenu() {
    var self = this;

    this.init = function() {
        $('#admin_menu_dropdown').dropdown();
    };
}

var mobileMenu = new MobileMenu();

arikaim.page.onReady(function() {
    mobileMenu.init();    
});
