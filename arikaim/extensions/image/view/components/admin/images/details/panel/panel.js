'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.button('.delete-image-button',function(element) {
        var uuid = $(element).attr('uuid');
        var title = $(element).attr('data-title');

        modal.confirmDelete({ 
            title: 'Delete Image',
            description: 'Confirm delete image ' + title
        },function() {
            imageControlPanel.delete(uuid,function(result) {
                arikaim.events.emit('image.delete',result);   
            });
        });
    });
});