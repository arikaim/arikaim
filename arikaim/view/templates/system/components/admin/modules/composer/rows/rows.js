'use strict';

arikaim.component.onLoaded(function() {
    safeCall('composerPackages',function(obj) {
        obj.initRows();
    },true); 
});