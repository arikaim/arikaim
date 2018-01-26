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
    var component_name = 'system:admin.system.logs';
    var component = arikaim.getComponent(component_name);

    this.init = function() {
        $('.delete-logs').off();
        $('.delete-logs').on('click',function() {
            var title = component.getProperty('messages.confirm.delete.title');
            var description = component.getProperty('messages.confirm.delete.description');
            confirmDialog.show({ title: title, description: description },function(){
                // confirmed
                self.clear(function() {
                    // reload component                   
                    self.reload();
                });
            });        
        });
    };   

    this.reload = function() {
        console.log('reload 1');
    };

    this.clear = function(onDone,onError) {
        arikaim.delete('/admin/api/logs/',onDone,onError);
    }
}

var systemLogs = new SystemLogs();

arikaim.onPageReady(function() {
    systemLogs.init();
});