/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function SystemSettings() {
   
    this.changeUserDetails = function(formId, onSuccess, onError) {
        return arikaim.post('/core/api/user/details',formId,onSuccess,onError);
    };

    this.changeUserPassword = function(formId, onSuccess, onError) {
        return arikaim.post('/core/api/user/password',formId,onSuccess,onError);
    };

    this.disableInstallPage = function(value, onSuccess, onError) {
        var data = { 
            install_page: value 
        };

        return arikaim.put('/core/api/settings/install-page',data,onSuccess,onError);
    };

    this.init = function() {   
        arikaim.ui.tab('.settings-item','settings_content');
    };
}

var settings = new SystemSettings();

arikaim.component.onLoaded(function() {    
    settings.init();
});
