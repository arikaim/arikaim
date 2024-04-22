/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
*/
'use strict';

function ViewTypeButton() {
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

    this.switchViewType = function(viewType) {
        return (viewType == 'table') ? 'grid' : 'table';
    };

    this.init = function(onSuccess) {       
        $('.view-type').on('click',function() {
            var namespace = $(this).attr('namespace');  
            var viewType = $(this).attr('data-value');   
            viewType = self.switchViewType(viewType);
            
            self.setViewType(viewType,namespace,function(result) {
                var iconClass = (result.view == 'grid') ? 'icon view-type-icon th' : 'icon view-type-icon list';               
                $('.view-type').attr('data-value',result.view);
                $('.view-type-icon').attr('class',iconClass);    
                callFunction(onSuccess,result.view);          
            });
        });  
    };
};

var viewTypeButton = new ViewTypeButton();
