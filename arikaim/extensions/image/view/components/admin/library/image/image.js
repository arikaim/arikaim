'use strict';

arikaim.component.onLoaded(function() { 
    arikaim.ui.button('.delete-image-button',function(element) {
        imagesLibrary.updateMainRelation(null,function(result) {
            $('#model_main_image').attr('src',result.image_src);
        });
    });
});