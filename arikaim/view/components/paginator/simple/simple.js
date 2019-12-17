arikaim.events.on('paginator.load.page',function(result) {    
    $('#current_page').html(result.page);

    if (result.page < result.last_page) {
        arikaim.ui.show('.next-page');     
    } else {
        arikaim.ui.hide('.next-page');   
        arikaim.ui.show('.prev-page');      
    }
    
    if (result.page == 1) {
        arikaim.ui.hide('.prev-page');
    } else {
        arikaim.ui.show('.prev-page');      
    }
},'simplePaginator');