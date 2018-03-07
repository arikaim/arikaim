/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function Paginator(current_page) {

    var component_name = "";
    var element_id;
    var page = 1;
    var self = this;

    this.init = function(component,element) {
        
        element_id = element;
        self.setComponentName(component);
        $('.page-link').off();
        $('.page-link').on('click',function() {
            self.setPage($(this).attr('page'));
            self.loadRows();  
        });

        $('.page-size-menu').off();
        $('.page-size-menu').dropdown({
            onChange: function(value) {
                self.loadRows(value);
            }
        });
    };

    this.setComponentName = function(component) {
        if (isEmpty(component) == false) {
            component_name = component;
        }
    };

    this.setPage = function(current_page) {
        if (isEmpty(current_page) == true || current_page < 1) {
            current_page = 1;           
        } 
        page = current_page;
    };

    this.loadRows = function(rows_per_page) {  
        if (isEmpty(component_name) == true) {
            return false;
        }
        if (isEmpty(element_id) == true) {
            return false;
        }
        var params = [page];
        if (isEmpty(rows_per_page) == false) {
            var params = [page,rows_per_page];    
        }
        arikaim.page.loadContent({
            id: element_id,
            component: component_name,
            params: params 
        },function(result) {          
            self.init();
        });
    };
    
    // init
    this.setPage(current_page);
}

var paginator = new Paginator();
