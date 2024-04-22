'use strict';

arikaim.component.onLoaded(function() {
    safeCall('contentView',function(obj) {
        obj.initRows();
    },true);    
});