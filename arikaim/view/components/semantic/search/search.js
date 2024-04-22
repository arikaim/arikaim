/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function Search() {
    var self = this;   
    this.options = {};

    this.init = function(options, namespace) {
        this.options = options;
        var formId = getValue('form_id',options,"#search_form");   
        $('.search-fields').dropdown({           
            allowAdditions: true
        });   
     
        arikaim.ui.button('.clear-search-form',function(element) {
            arikaim.ui.form.clear(formId);
            $(formId).form('clear');
            return self.clear(namespace,function(result) {                  
                self.loadResult();                       
            });           
        });

        arikaim.ui.form.onSubmit(formId,function() {
            var items = self.getSearchFields(formId);
            var data = { search: items };
            return self.setSearch(data,namespace,function(result) {              
                self.loadResult();               
            });
        });
    };

    this.getSearchFields = function(formId) {
        var items = {};
        $(formId).find('.search-field').each(function(index) {
            var name = $(this).attr('name');
            var value = $(this).val();
            items[name] = value;
        });   

        return items;
    };
    
    this.clear = function(namespace, onSuccess, onError) {
        namespace = getDefaultValue(namespace,'');

        return arikaim.delete('/core/api/ui/search/' + namespace,onSuccess,onError);          
    };

    this.setSearch = function(searchData, namespace, onSuccess, onError) {
        namespace = getDefaultValue(namespace,'');  
        searchData.namespace = namespace;    

        return arikaim.put('/core/api/ui/search/',searchData,onSuccess,onError);           
    };

    this.loadResult = function(onSuccess, onError) {  
        if (isEmpty(this.options.component) == true) {
            return false;
        }       
        var event = getValue('event',this.options,'search.load');
        var params = getValue('params',this.options,null);

        if (isFunction(this.options.beforeLoadResult) == true) {
            callFunction(this.options.beforeLoadResult,this);
        }
        
        return arikaim.page.loadContent({
            id: self.options.id,
            component: self.options.component,
            params: params
        },function(result) {
            arikaim.events.emit(event,result);
            callFunction(onSuccess,result);
        },onError);
    };
}

var search = new Search();
