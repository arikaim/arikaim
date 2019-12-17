/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function SystemLogs() {
    var self = this;
    var component = arikaim.component.get('system:admin.system.logs');

    this.init = function() {
        arikaim.ui.button('.delete-logs',function(element) {
            var title = component.getProperty('messages.confirm.delete.title');
            var description = component.getProperty('messages.confirm.delete.description');

            return modal.confirmDelete({ 
                title: title,
                description: description 
            }).done(function() {                
                self.clear().done(function() {           
                    self.reload();
                });
            });        
        });
    };   

    this.reload = function() {
        arikaim.page.loadContent({
            id: 'tab_content',
            component: 'system:admin.system.logs'
        });
    };

    this.clear = function(onSuccess, onError) {
        return arikaim.delete('/core/api/logs/clear',onSuccess,onError);
    }
}

var systemLogs = new SystemLogs();

arikaim.page.onReady(function() {
    systemLogs.init();
});
