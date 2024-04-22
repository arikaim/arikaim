/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
*/
'use strict';

function ViewType() {
    var self = this;

    this.getViewType = function(namespace, onSuccess, onError) {       
        namespace = getDefaultValue(namespace,'');

        return arikaim.get('/core/api/ui/paginator/view/type' + namespace,onSuccess,onError);           
    };

    this.setViewType = function(viewType, namespace, onSuccess, onError) {
        viewType = (isEmpty(viewType) == true) ? 'table' : viewType;
        namespace = getDefaultValue(namespace,'');
        var data = { 
            view: viewType,
            namespace: namespace  
        };

        return arikaim.put('/core/api/ui/paginator/view/type',data,onSuccess,onError);          
    };

    this.init = function(onSuccess) {
        var namespace = $('.view-type').attr('namespace');

        $('.view-type').dropdown({
            onChange: function(value) {                
                self.setViewType(value,namespace,function(result) {
                    callFunction(onSuccess,result.view);                           
                });             
            }
        });  
    };
};

var viewType = new ViewType();
