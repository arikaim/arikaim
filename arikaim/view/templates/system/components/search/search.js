/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function Search() {

    var search_result_element;
    var search_filed_filter_id = "#fields_filter";
    var search_button_element = "#search_button";
    var search_text_field_id  = "#search_text";
    var load_component;
    var self = this;

    this.init = function(component,result_element) {
        $('.search-fields').dropdown({           
            allowAdditions: true
        });
        $('.search-actions').dropdown();

        if (isEmpty(component) == true) {
            load_component = $(search_button_element).attr('component');
        } else {
            load_component = component;
        }
        if (isEmpty(result_element) == true) {
            search_result_element = $(search_button_element).attr('search-result');
        } else {
            search_result_element = result_element;
        }

        $(search_button_element).off();
        $(search_button_element).on('click',function() {                     
            self.run();
        });

        $('.clear-input').off();
        $('.clear-input').on('click',function() {
            self.clear(function() {
                self.load();
            });           
        });
    };

    this.clear = function(onSuccess) {
        $(search_text_field_id).val('');
        arikaim.session.set('search','',function(result) {
            callFunction(onSuccess,result);
        });
    };

    this.getSearchFields = function() {
        var all =  {"field": "all","operator": "LIKE","statement_operator": "or"};
        var fields = $(search_filed_filter_id);
        if (isObject(fields) == true) {
            fields = fields.val().split(',');
        } else {
            fields = [];
        }       
        if ((fields.length > 0) && (fields[0] != "")) {
            var items = [];
            for (var i = 0; i < fields.length; i++) {               
                var item = {"field": fields[i],"operator": "LIKE","statement_operator": "or"};
                items.push(item);
            }   
            fields = items;
        } else {          
            fields = all;
        }
        return fields;
    };

    this.getSearchData = function() {
        var search = $(search_text_field_id).val();
        var fields = this.getSearchFields();
        var data = {"search": search,"fields": fields};
        return JSON.stringify(data);
    };

    this.run = function(onSuccess) {
        var search_data = this.getSearchData();
        // set session
        arikaim.session.set('search',search_data,function(result) {
            self.load(onSuccess)
        });
    };

    this.load = function(onSuccess) {
        var params = [1];
        arikaim.page.loadContent({
            id: search_result_element,
            component: load_component,
            params: params
        },function(result) {         
            callFunction(onSuccess,result);
        });
    };
}

var search = new Search();

arikaim.page.onReady(function() {
    search.init();
});