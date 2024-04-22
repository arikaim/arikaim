'use strict';

arikaim.component.onLoaded(function() {
    safeCall('blogPostView',function(obj) {
        obj.initRows();
    },true);    
});