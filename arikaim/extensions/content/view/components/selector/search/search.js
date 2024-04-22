'use strict';

arikaim.component.onLoaded(function() {
    $('.content-search-dropdown').on('change',function() {     
        var selected = $('.content-search-dropdown').dropdown('get value');
        var provider = $(this).attr('content-provider');
        var type = $(this).attr('content-type');

        arikaim.page.loadContent({
            id: 'search_result_content',           
            component: 'content::selector.search.result',
            params: { 
                uuid: selected,
                provider: provider,
                type: type               
            }
        });
    });
});