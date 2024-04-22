'use strict';

arikaim.component.onLoaded(function() {    
    safeCall('arikaimStoreView',function(obj) {
        obj.initRows();
    },true);  
});