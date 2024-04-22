/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function SystemLogs() {
    var self = this;
  
    this.init = function() {
        this.loadMessages('system:admin.system.logs');

        arikaim.ui.button('.delete-logs',function(element) {
            return modal.confirmDelete({ 
                title: self.getMessage('confirm.delete.title'),
                description: self.getMessage('confirm.delete.description') 
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

var systemLogs = createObject(SystemLogs,ControlPanelView);

arikaim.component.onLoaded(function() {    
    systemLogs.init();
});
