'use strict';

arikaim.component.onLoaded(function(component) {

    arikaim.events.on('oauth.success',function(result) {
        drivers.reloadConfig();
    },'fetchAccessToken');

    arikaim.ui.button('.oauth-authorize-button',function(element) {
        var provider = $(element).attr('provider');
        var action = $(element).attr('action');
        var redirectUrl = $(element).attr('redirect');
        var configName = $(element).attr('config-name');

        oauth.openAuthWindow(provider,action,redirectUrl,null,configName);        
    });
});