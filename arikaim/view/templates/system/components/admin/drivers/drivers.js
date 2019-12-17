/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Drivers() {
    var self = this;
  
    this.enable = function(name, onSuccess, onError) {
        var data = {
            name: name,
            status: 1
        };
        return arikaim.put('/core/api/driver/status',data,onSuccess,onError);          
    };

    this.disable = function(name, onSuccess, onError) {
        var data = {
            name: name,
            status: 0
        };
        return arikaim.put('/core/api/driver/status',data,onSuccess,onError);          
    };

    this.getConfig = function(name, onSuccess, onError) {
        return arikaim.get('/core/api/driver/config/' + name,onSuccess,onError);
    };

    this.saveConfig = function(config, onSuccess, onError) {
        return arikaim.put('/core/api/driver/config',config,onSuccess,onError);
    };

    this.loadConfigForm = function(name, elementId, onSuccess) {
        return arikaim.page.loadContent({
            id: elementId,
            params: { name: name },
            component: 'system:admin.modules.drivers.config.form'
        },function(result) {                  
            callFunction(onSuccess,result);
        });
    };

    this.loadConfig = function(name, elementId, onSuccess) {
        return arikaim.page.loadContent({
            id: elementId,
            params: { driver_name: name },
            component: 'system:admin.modules.drivers.config'
        },function(result) {                  
            callFunction(onSuccess,result);
        });
    }; 
}

var drivers = new Drivers();
