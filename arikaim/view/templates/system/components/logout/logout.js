'use strict';

arikaim.component.onLoaded(function() {    
    user.logout(function() {
        arikaim.clearToken();
        arikaim.page.reload();      
    });
});