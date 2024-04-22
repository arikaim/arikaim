'use strict';

arikaim.component.onLoaded(function() {    
    safeCall('relationsView',function(obj) {
        obj.initRows();
    },true);      
}); 