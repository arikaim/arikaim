'use strict';

arikaim.component.onLoaded(function() {    
    safeCall('fileLogsView',function(obj) {
        obj.initRows();
    },true);   
}); 