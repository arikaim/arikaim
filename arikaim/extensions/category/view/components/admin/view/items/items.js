$(document).ready(function(result) {
    safeCall('categoryView',function(obj) {
        obj.initRows();
    },true);  
});