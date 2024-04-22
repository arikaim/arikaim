'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.form.onSubmit("#category_form",function() {  
        return arikaim.post('/api/admin/category/add','#category_form');
    },function(result) {
        arikaim.ui.form.clear('#category_form');
        arikaim.ui.form.showMessage(result.message);
        // load edit category       
        category.loadEditCategory(result.uuid);
    });
});
