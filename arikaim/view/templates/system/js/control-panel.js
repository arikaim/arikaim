/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ControlPanelView(child) {
    var self = this;
    var messagesComponentName = null;

    var itemComponentName = null;
    var itemSelector = 'row_';
    var itemsSelector = 'view_items';

    this.messages = null;

    this.setItemsSelector = function(selector) {
        itemsSelector = selector;
    };

    this.setItemSelector = function(selector) {
        itemSelector = selector;
    };

    this.setItemComponentName = function(name) {
        itemComponentName = name
    };

    this.deleteItem = function(key) {
        $('#' + itemSelector + key).remove();                
    };

    this.updateItem = function(uuid) {
        return arikaim.page.loadContent({
            id: itemSelector + uuid,         
            replace: true,
            component: itemComponentName,
            params: {
                uuid: uuid
            }
        },function() {
            child.initRows();
        });
    };

    this.addItem = function(params) {
        return arikaim.page.loadContent({
            id: itemsSelector,         
            prepend: true,  
            component: itemComponentName,
            params: params
        },function() {
            child.initRows();
        });
    };

    this.setMessagesComponent = function(name) {
        messagesComponentName = name;
    };

    this.getMessage = function(key) {
        if (isObject(this.messages) == false) {
            this.loadMessages(null,function(messages) {              
                return getValue(key,messages,'');
            });
        }
      
        return getValue(key,this.messages,'');
    };

    this.loadMessages = function(componentName, onSuccess) {
        if (isObject(this.messages) == true) {
            callFunction(onSuccess,this.messages);
            return;
        }
        componentName = getDefaultValue(componentName,messagesComponentName);

        arikaim.component.loadProperties(componentName,function(params) { 
            self.messages = params.messages;
            callFunction(onSuccess,params.messages);
        }); 
    };

    this.init = function() {};
    this.initRows = function() {};
    this.loadRows = function() {};
}