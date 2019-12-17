/**
 *  Arikaim  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Languages() {
    var self = this;

    this.delete = function(uuid, onSuccess, onError) {        
        return arikaim.delete('/core/api/language/' + uuid,onSuccess,onError); 
    };
    
    this.setDefault = function(uuid, onSuccess, onError) {
        var data = { uuid: uuid };
        return arikaim.put('/core/api/language/default',data,onSuccess,onError);        
    };

    this.loadMenu = function(selector) {
        selector = getDefaultValue(selector,"language_menu");        
        arikaim.page.loadContent({
            id : selector,
            component : 'components:language.dropdown'
        },function(result) {            
            $('#language_dropdown').dropdown({
                onChange: function(value) {           
                    arikaim.setLanguage(value);
                }               
            });
        });
    };

    /**
     * 
     * @param string uuid 
     * @param int status  0 - disabled, 1 - active, 2 - default
     */
    this.setStatus = function(uuid, status, onSuccess, onError) {     
        var status = isEmpty(status) ? 'toggle' : status;     
        var data = { 
            uuid: uuid,
            status: status 
        };    

        return arikaim.put('/core/api/language/status',data,onSuccess,onError);      
    };

    this.load = function(uuid, onSuccesss, onError) {
        return arikaim.page.loadContent({
            id : 'form_content',
            component : 'system:admin.languages.language.form',
            params: { uuid: uuid },
            loader : false
        },onSuccesss,onError);
    };
  
    this.init = function() {
        arikaim.ui.tab();
    };
}

var languages = new Languages();

arikaim.page.onReady(function() {
    languages.init();
});
