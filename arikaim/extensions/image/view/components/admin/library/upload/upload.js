'use strict';

arikaim.component.onLoaded(function() {  
    arikaim.events.on('image.upload',function(result) {  
        arikaim.ui.setActiveTab('#library_tab');
    },'imageLibraryUpload');
});