'use strict';

arikaim.component.onLoaded(function() {
    $('#select_image').dropdown({
        onChange: function(value, text, choice) {            
            arikaim.page.loadContent({
                id: 'relations_content',
                component: 'system:admin.orm.relations.view',
                params: { 
                    extension: 'image', 
                    model: 'ImageRelations',                    
                    id: value 
                }
            },function(result) {               
            });  
        }
    });
});