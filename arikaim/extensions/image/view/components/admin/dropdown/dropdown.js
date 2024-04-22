'use strict';

arikaim.component.onLoaded(function() {
    var dataField = $('.image-dropdown').attr('data-field');

    $('.image-dropdown').dropdown({
        apiSettings: {     
            on: 'now',      
            url: arikaim.getBaseUrl() + '/api/admin/image/list/' + dataField + '/{query}',   
            cache: false        
        },
        filterRemoteData: false         
    });
});