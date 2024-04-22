/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function OrderBy() {
    var self = this;
    var options = null;

    this.init = function(elementId, componentName, namespace) {
        this.setOptions(elementId,componentName, namespace);

        arikaim.ui.button('.order-by-link',function(element) {
            var field = $(element).attr('order-by');
            var type = $(element).attr('order-by-type');
          
            self.set(field,type,self.getOptions().namespace,function(result) {
                self.loadResult(self.getOptions());               
                self.toggleType(element,type);
            });
        });       
    };

    this.toggleType = function(element,type) {
        $('.order-by-link').html("<i class='icon sort'><i>");

        if (type == 'asc') {
            $(element).attr('order-by-type','desc');          
            $(element).html("<i class='icon sort down'><i>");
        }
        if (type == 'desc') {
            $(element).attr('order-by-type','asc');           
            $(element).html("<i class='icon sort up'><i>");
        }
    };

    this.setOptions = function(elementId, componentName, namespace) {
        namespace = getDefaultValue(namespace,"");
        options = { 
            id: elementId,
            component: componentName,
            namespace: namespace 
        };
    };

    this.getOptions = function() {      
        return options;
    }

    this.get = function(namespace, onSuccess, onError) {
        namespace = (isEmpty(namespace) == true) ? "" : namespace;

        return arikaim.get('/core/api/ui/order/' + namespace,onSuccess,onError);          
    };

    this.delete = function(namespace, onSuccess, onError) {
        namespace = (isEmpty(namespace) == true) ? "" : namespace;

        return arikaim.delete('/core/api/ui/order/' + namespace,onSuccess,onError);           
    };

    this.set = function(fieldName, type, namespace, onSuccess, onError) {
        namespace = (isEmpty(namespace) == true) ? "" : namespace;
        var data = { 
            field: fieldName,
            type: type 
        };

        return arikaim.put('/core/api/ui/order/' + namespace,data,onSuccess,onError);          
    };

    this.loadResult = function(options, onSuccess, onError) {  
        if (isObject(options) == false) {
            return false;
        }    

        return arikaim.page.loadContent({
            id: options.id,
            component: options.component
        },onSuccess,onError);          
    };
}

var order = new OrderBy();
