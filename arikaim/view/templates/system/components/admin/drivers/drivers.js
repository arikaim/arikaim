/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function Drivers() {    
    this.currentConfigDriver = null;
    this.currentConfigElementId = null;
    this.currentConfigColumnClass = null;

    this.uninstall = function(name, onSuccess, onError) {
        return arikaim.delete('/core/api/driver/uninstall/' + name,onSuccess,onError);      
    };

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

    this.saveConfig = function(config, onSuccess, onError) {
        return arikaim.put('/core/api/driver/config',config,onSuccess,onError);
    };

    this.loadConfigForm = function(name, elementId, onSuccess) {
        this.currentConfigDriver = name;
        this.currentConfigElementId = elementId;

        return arikaim.page.loadContent({
            id: elementId,
            params: { driver_name: name },
            component: 'system:admin.modules.drivers.config.form'
        },function(result) {                  
            callFunction(onSuccess,result);
        });
    };

    this.reloadConfig = function(onSuccess) {
        if (isEmpty(this.currentConfigDriver) == true || isEmpty(this.currentConfigElementId) == true) {
            return false;
        }

        return arikaim.page.loadContent({
            id: this.currentConfigElementId,
            params: { 
                update: true,
                driver_name: this.currentConfigDriver, 
                column_class: this.currentConfigColumnClass
            },
            component: 'system:admin.modules.drivers.config'
        },function(result) {                  
            callFunction(onSuccess,result);
        });
    };

    this.loadConfig = function(name, elementId, onSuccess, columnClass) {
        this.currentConfigDriver = name;
        this.currentConfigElementId = elementId;
        this.currentConfigColumnClass = columnClass;

        return arikaim.page.loadContent({
            id: elementId,
            params: { 
                driver_name: name, 
                column_class: columnClass
            },
            component: 'system:admin.modules.drivers.config'
        },function(result) {                  
            callFunction(onSuccess,result);
        });
    }; 
}

var drivers = new Drivers();
