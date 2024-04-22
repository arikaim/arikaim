'use strict';

arikaim.component.onLoaded(function() {    
    safeCall('eventsView',function(obj) {
        obj.initRows();
    },true);   
}); 