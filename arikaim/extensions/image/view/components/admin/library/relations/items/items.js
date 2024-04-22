'use strict';

arikaim.component.onLoaded(function() {   
    safeCall('imagesLibraryRelations',function(obj) {
        obj.initRows();
    },true);   
}); 