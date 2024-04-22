'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.form.onSubmit("#meta_tags_form",function() {            
        return arikaim.put('/api/admin/category/update/meta','#meta_tags_form');
    },function(result) {
        arikaim.ui.form.showMessage(result.message);
    });
});