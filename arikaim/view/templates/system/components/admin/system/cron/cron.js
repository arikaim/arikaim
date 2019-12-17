/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Cron() {
    var self = this;

    this.init = function() {   
        $('.jobs-list').accordion();

        arikaim.ui.button('#install_cron',function(element) {
            return self.install().done(function() {
                arikaim.page.loadContent({
                    id: 'tab_content',
                    component: "system:admin.system.cron"
                });
            });
        });

        arikaim.ui.button('#uninstall_cron',function(element) {
            return self.unInstall(function(result) {
                arikaim.page.loadContent({
                    id: 'tab_content',
                    component: "system:admin.system.cron"
                });
            });
        });
    };

    this.install = function(onSuccess, onError) {
        return arikaim.put('/core/api/queue/cron/install',{},onSuccess,onError);          
    };

    this.unInstall = function(onSuccess, onError) {
        return arikaim.delete('/core/api/queue/cron/uninstall',onSuccess,onError);          
    };
}

var cron = new Cron();

arikaim.page.onReady(function() {
    cron.init();    
});
