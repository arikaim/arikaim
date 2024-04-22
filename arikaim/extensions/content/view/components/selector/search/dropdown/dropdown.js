'use strict';

arikaim.component.onLoaded(function() {
    var dataField = $('.content-search-dropdown').attr('data-field');
    var contentSelector = $('.content-search-dropdown').attr('content-selector');

    $('.content-search-dropdown').dropdown({
        apiSettings: {     
            on: 'now',      
            url: arikaim.getBaseUrl() + '/api/content/search/list/' + dataField + '/' + contentSelector + '/{query}',   
            cache: false        
        },       
        filterRemoteData: false         
    });
});