'use strict';

arikaim.component.onLoaded(function() {    
    safeCall('dbLogsView',function(obj) {
        obj.initRows();
    },true);   
}); 