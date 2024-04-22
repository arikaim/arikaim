'use strict';

arikaim.component.onLoaded(function() {
    imageUpload.onSuccess = function(result) {
        arikaim.page.loadContent({
            id: 'image_content',
            params: { uuid: result.uuid },
            component: 'image::admin.images.view'
        });
        arikaim.ui.setActiveTab('#view_images','.image-tab-item');
    };
});