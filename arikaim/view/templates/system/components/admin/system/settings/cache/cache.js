/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Cache() {
    var self = this;

    this.enable = function(onSuccess, onError) {      
        return arikaim.put('/core/api/cache/enable',null,onSuccess,onError);
    };

    this.disable = function(onSuccess, onError) {      
        return arikaim.put('/core/api/cache/disable',null,onSuccess,onError);
    };

    this.clear = function(onSuccess, onError) {      
        return arikaim.delete('/core/api/cache/clear',onSuccess,onError);
    };

    this.init = function() {   
        $('.enable-dropdown').dropdown({
            onChange: function(value) {
                if (value == 1) {
                    arikaim.ui.show('#clear_cache_button');
                    self.enable(function(result) {
                        arikaim.page.loadContent({
                            id: 'cache_info',
                            component: "system:admin.system.settings.cache.info"
                        });
                    });
                } else {
                    arikaim.ui.hide('#clear_cache_button');
                    self.disable(function(result) {
                        $('#cache_info').html('');
                    });
                }
            }
        });

        arikaim.ui.button('#clear_cache_button',function(element) {          
            return self.clear(function(result) {  
                var message = result.message;              
                arikaim.page.loadContent({
                    id: 'cache_info',
                    component: "system:admin.system.settings.cache.info"
                },function(result) {
                    arikaim.ui.form.showMessage({
                        selector: '#message',
                        message: message
                    });
                });                             
            },function(error) {
                arikaim.ui.form.showMessage({
                    selector: '#message',
                    message: error,
                    class: 'error',
                    removeClass: 'success'
                });
            });
        });
    };
}

var cache = new Cache();

$(document).ready(function() {
    cache.init();
});
