'use strict';

arikaim.component.onLoaded(function() {    
    arikaim.ui.button('.image-preview-button',function(element) {  
        var image = $(element).attr('data-src');
        templatesView.showImagePreview({
            images: [image]
        });
        
        return true;
    });

    packageRepository.onInstalled = function(result) {      
        templates.showDetailsPage(result.name);
    };
});