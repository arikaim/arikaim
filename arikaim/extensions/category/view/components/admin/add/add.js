arikaim.page.onReady(function() {
    arikaim.ui.form.onSubmit("#category_form",function() {  
        return arikaim.post('/api/category/admin/add','#category_form');
    },function(result) {
        arikaim.ui.form.clear('#category_form');
        arikaim.ui.form.showMessage(result.message);
        // reload dropdown
        var parentId = $('parent_id').val();
        var language = $('language').val();

        arikaim.page.loadContent({
            id: 'parent_category',
            component: 'category::dropdown',
            params: { 
                parent_id: parentId, 
                language: language,
                name: 'parent_id', 
                class: 'basic',
                root: { title: '/' }  
            }
        },function(result) {
            category.initCategoryDropDown();
        });   
    });
});
