/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function Theme() {

    var self = this;
    var template_name = "system";
    var themes_dropdown_element = "#themes_dropdown";

    this.init = function() {
       
        template_name = $(themes_dropdown_element).attr('template');
        $(themes_dropdown_element).dropdown({
            onChange: function(value) {    
                self.save(value,template_name,function() {
                    arikaim.page.reload();
                });
            }
        });
    };

    this.save = function(theme_name,template_name,onSuccess) {
        var data = { 
            theme_name: theme_name,
            template_name: template_name 
        };
        arikaim.put("/admin/api/template/theme/current/",data,onSuccess);
    };
}

var theme = new Theme();

arikaim.page.onReady(function() {
    theme.init();
});