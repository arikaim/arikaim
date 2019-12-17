/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Search() {
    var self = this;   
    this.options = {};

    this.init = function(options, namespace, onSuccess) {
        this.options = options;
        var formId = getValue('form_id',options,"#search_form");
        
        $('.search-fields').dropdown({           
            allowAdditions: true
        });   
        $('.search-actions').dropdown();

        arikaim.ui.button(formId + ' .clear-form',function(element) {
            arikaim.ui.form.clear(formId);
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
        namespace = getDefaultValue(namespace,"");
        return arikaim.delete('/core/api/ui/search/' + namespace,onSuccess,onError);          
    };

    this.setSearch = function(searchData, namespace, onSuccess, onError) {
        namespace = getDefaultValue(namespace,"");  
        searchData.namespace = namespace;    
        return arikaim.put('/core/api/ui/search/',searchData,onSuccess,onError);           
    };

    this.loadResult = function(onSuccess, onError) {  
        if (isEmpty(this.options.component) == true) {
            return false;
        }       
        var event = getValue('event',this.options,'search.load');

        return arikaim.page.loadContent({
            id: self.options.id,
            component: self.options.component
        },function(result) {
            arikaim.events.emit(event,result);
            callFunction(onSuccess,result);
        },onError);
    };
}

if (isEmpty(search) == true) {
    var search = new Search();
}
