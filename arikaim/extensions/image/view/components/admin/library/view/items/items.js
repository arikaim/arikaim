'use strict';

arikaim.component.onLoaded(function() {   
    safeCall('imagesLibrary',function(obj) {
        obj.initRows();
    },true);  
}); 