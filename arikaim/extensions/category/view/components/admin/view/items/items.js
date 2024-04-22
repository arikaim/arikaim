'use strict';

arikaim.component.onLoaded(function() {
    safeCall('categoryView',function(obj) {
        obj.initRows();
    },true);  
});