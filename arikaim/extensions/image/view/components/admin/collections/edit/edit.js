'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.form.onSubmit('#collection_form',function() {
        return imageCollectionsControlPanel.update('#collection_form');
    },function(result) {        
        arikaim.ui.form.showMessage({
            message: result.message
        });
    });
});