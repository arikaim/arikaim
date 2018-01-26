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
    var search_button_element = "#search_button";
    var search_text_field_id  = "#search_text";
    var load_component;
    var search_data = {};
    var self = this;

    this.init = function() {

        $('.search-fields').dropdown();
        $('.search-actions').dropdown();

        load_component = $(search_button_element).attr('component');
        search_result_element = $(search_button_element).attr('search-result');
        $(search_button_element).off();
        $(search_button_element).on('click',function() {                     
            self.run();
        });

        $('.clear-input').off();
        $('.clear-input').on('click',function() {
            //console.log('clear');
            self.clear(function() {
                self.load();
            });           
        });
    };

    this.clear = function(onDone) {
        $(search_text_field_id).val('');
        arikaim.session.set('search','',function(result) {
            callFunction(onDone,result);
        });
    };

    this.run = function(onDone) {
        search_data.value = $(search_text_field_id).val();
        // set session
        arikaim.session.set('search',search_data,function(result) {
            self.load(onDone)
        });
    };

    this.load = function(onDone) {
        var params = [1];
        arikaim.page.loadContent({
            id: search_result_element,
            component: load_component,
            params: params
        },function(result) {         
            callFunction(onDone,result);
        });
    };
}

var search = new Search();

arikaim.onPageReady(function() {
    search.init();
});