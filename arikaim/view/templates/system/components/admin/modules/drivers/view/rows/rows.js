'use strict';

arikaim.component.onLoaded(function() {    
    safeCall('driversView',function(obj) {
        obj.initRows();
    },true);   
});