/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function SystemLogs() {

    var self = this;
    var component = arikaim.getComponent('system:admin.system.logs');

    this.init = function() {
        $('.delete-logs').off();
        $('.delete-logs').on('click',function() {
            var title = component.getProperty('messages.confirm.delete.title');
            var description = component.getProperty('messages.confirm.delete.description');
            confirmDialog.show({ 
                title: title,
                description: description 
            },function() {                
                self.clear(function() {           
                    self.reload();
                });
            });        
        });
    };   

    this.reload = function() {
        arikaim.page.loadContent({
            id: 'system_tab',
            component: 'system:admin.system.logs'
        })
    };

    this.clear = function(onSuccess,onError) {
        arikaim.delete('/admin/api/logs/',onSuccess,onError);
    }
}

var systemLogs = new SystemLogs();

arikaim.page.onReady(function() {
    systemLogs.init();
});