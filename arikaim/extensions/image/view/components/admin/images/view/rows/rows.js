'use strict';

arikaim.component.onLoaded(function() {   
    safeCall('imagesView',function(obj) {
        obj.initRows();
    },true);   
}); 