'use strict';

arikaim.component.onLoaded(function() {
    safeCall('contentProvidersView',function(obj) {
        obj.initRows();
    },true);   
});