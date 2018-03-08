/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function CoreModules() {

    var update_button = '#update_button';
    var self = this;

    this.init = function() {
        $('.modules-list').accordion();
        $(update_button).off();
        $(update_button).on('click',function() {
            $(this).addClass('loading');
            self.update();
        });
    };

    this.update = function() {
        arikaim.get('/admin/api/modules/update',function(result) {
            arikaim.page.loadContent({
                id: 'system_tab',
                component: 'system:admin.system.modules'
            });
            $(update_button).removeClass('loading');
        },function (errors) {
            $(update_button).removeClass('loading');
        });
    };
}

var modules = new CoreModules();

arikaim.page.onReady(function() {
    modules.init();
});
