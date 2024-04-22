'use strict';

arikaim.component.onLoaded(function() { 
    arikaim.ui.form.onSubmit('#collection_form',function() {
        return imageCollectionsControlPanel.add('#collection_form');
    },function(result) {
        arikaim.ui.form.clear('#collection_form');      
        arikaim.ui.form.showMessage({
            message: result.message
        });
    });
});
