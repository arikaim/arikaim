'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.form.addRules("#import_image_form");

    arikaim.ui.form.onSubmit("#import_image_form",function() {  
        return arikaim.post('/api/admin/image/import','#import_image_form',function(result) {
            arikaim.events.emit('image.import',result);           
        });
    });
});