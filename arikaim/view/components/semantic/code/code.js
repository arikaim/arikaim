'use strict';

arikaim.component.onLoaded(function(component) {
    safeCall('hljs',function(obj) {   
        obj.highlightAll();
    },true);    
});