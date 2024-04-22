'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.form.onSubmit("#category_description_form",function() {            
        return arikaim.put('/api/admin/category/update/description','#category_description_form');
    },function(result) {
        arikaim.ui.form.showMessage(result.message);
    });
});