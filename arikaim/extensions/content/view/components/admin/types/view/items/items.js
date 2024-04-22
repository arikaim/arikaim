'use strict';

arikaim.component.onLoaded(function() {
    safeCall('contentTypesView',function(obj) {
        obj.initRows();
    },true);   
});