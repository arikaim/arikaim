arikaim.page.onReady(function() {

    function initEditCategoryForm() {
        arikaim.ui.form.onSubmit("#category_form",function() {  
            return arikaim.put('/api/category/admin/update','#category_form');
        },function(result) {
            arikaim.ui.form.showMessage(result.message);
        });
    };

    $('#select_category').dropdown({
        allowCategorySelection: true,
      
        onChange: function(value, text, choice) { 
            var title = $(choice).attr('title');
            var language = $(choice).attr('language');            
            $(this).children('.text').html(title);
        
            arikaim.page.loadContent({
                id: 'form_content',
                component: 'category::admin.form',
                params: { uuid: value, language: language }
            },function(result) {
                initEditCategoryForm();
            });  
        }
    });

    initEditCategoryForm();
});