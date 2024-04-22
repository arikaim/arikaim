'use strict';

arikaim.component.onLoaded(function() {
    safeCall('collectionsView',function(obj) {
        obj.initRows();
    },true);   
});