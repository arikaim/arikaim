'use strict';

arikaim.component.onLoaded(function() {
    safeCall('trashView',function(obj) {
        obj.initRows();
    },true);  
});