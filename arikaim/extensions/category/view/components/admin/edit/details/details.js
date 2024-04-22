'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.form.onSubmit("#category_form",function() {            
        return arikaim.put('/api/admin/category/update','#category_form');
    },function(result) {
        arikaim.ui.form.showMessage(result.message);
    });
});