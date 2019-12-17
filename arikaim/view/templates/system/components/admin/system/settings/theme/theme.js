/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Theme() {

    var self = this;
    var templateName = "system";
    var themesDropdownSelector = "#themes_dropdown";

    this.init = function() {
       
        templateName = $(themesDropdownSelector).attr('template');
        $(themesDropdownSelector).dropdown({
            onChange: function(value) {    
                self.save(value,templateName,function() {
                    arikaim.page.reload();
                });
            }
        });
    };

    this.save = function(theme_name, templateName, onSuccess) {
        var data = { 
            theme_name: theme_name,
            template_name: templateName 
        };
        arikaim.put("/admin/api/template/theme/current/",data,onSuccess);
    };
}

var theme = new Theme();

arikaim.page.onReady(function() {
    theme.init();
});
