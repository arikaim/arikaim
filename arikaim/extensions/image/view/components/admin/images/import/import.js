'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.button('.load-image',function(element) {
        var url = $('#url').val().trim();
        if (isEmpty(url) == true) {
            return true;
        }
   
        arikaim.ui.loadImage(url,function(image) {
            arikaim.page.loadContent({
                id: 'import_image_content',
                component: 'image::admin.images.import.form',
                params: { url: url }
            });    
        });
    });  

    arikaim.events.on('image.import',function(params) {   
        return arikaim.page.loadContent({
            id: 'image_content',
            params: { uuid: params.uuid },
            component: 'image::admin.images.view'
        });
    });
});