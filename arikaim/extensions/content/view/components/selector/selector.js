'use strict';

arikaim.component.onLoaded(function() {
    $('.content-type-dropdown').dropdown({
        onChange: function(value) {
            return arikaim.page.loadContent({
                id: 'content_provider_selector',           
                component: 'content::selector.provider',
                params: { 
                    type: value,
                    class: 'fluid basic button selection' 
                }            
            },function(result) {
                $('#content_provider_dropdown').dropdown({
                    onChange: function(value) {
                        var type = $('.content-type-dropdown').dropdown('get value');
                      
                        return arikaim.page.loadContent({
                            id: 'search_content',           
                            component: 'content::selector.search',
                            params: { 
                                provider: value,
                                type: type                               
                            }            
                        });
                    }
                });
            }); 
        }
    });
});