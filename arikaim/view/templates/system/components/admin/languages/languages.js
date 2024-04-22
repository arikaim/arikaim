/**
 *  Arikaim  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function Languages() {
   
    this.delete = function(uuid, onSuccess, onError) {        
        return arikaim.delete('/core/api/language/' + uuid,onSuccess,onError); 
    };
    
    this.setDefault = function(uuid, onSuccess, onError) {
        return arikaim.put('/core/api/language/default',{ uuid: uuid },onSuccess,onError);        
    };

    /**
     * 
     * @param string uuid 
     * @param int status  0 - disabled, 1 - active, 2 - default
     */
    this.setStatus = function(uuid, status, onSuccess, onError) {     
        var status = isEmpty(status) ? 'toggle' : status;     
    
        return arikaim.put('/core/api/language/status',{ 
            uuid: uuid,
            status: status 
        },onSuccess,onError);      
    };

    this.load = function(uuid, onSuccesss, onError) {
        return arikaim.page.loadContent({
            id : 'form_content',
            component : 'system:admin.languages.language.form',
            params: { uuid: uuid },
            loader : false
        },onSuccesss,onError);
    };
}

var languages = new Languages();

arikaim.component.onLoaded(function() {  
    arikaim.ui.tab();
});
