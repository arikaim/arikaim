'use strict';

arikaim.component.onLoaded(function(component) {
    $('.page-go-menu').dropdown({
        onChange: function(value) { 
            var namespace = $(this).attr('namespace');
            paginator.setPage(value,namespace,function(result) {                         
                arikaim.events.emit('paginator.load.page',result);                                                     
                paginator.loadRows(function(result) {
                    paginator.reload();  
                });  
            });            
        }
    })  
});