'use strict';

arikaim.events.on('paginator.load.page',function(result) {    
    var page = parseInt(result.page);
    var lastPage = parseInt(result.last_page);

    $('#current_page').html(page);

    if (page < lastPage) {
        arikaim.ui.show('.next-page');     
    } else {
        arikaim.ui.hide('.next-page');   
        arikaim.ui.show('.prev-page');      
    }
    
    if (page == 1) {
        arikaim.ui.hide('.prev-page');
    } else {
        arikaim.ui.show('.prev-page');      
    }
},'simplePaginator');

arikaim.component.onLoaded(function(component) {
    var init = $('.paginator').attr('init');   
    if (init == true) {      
        paginator.init();
    }
});