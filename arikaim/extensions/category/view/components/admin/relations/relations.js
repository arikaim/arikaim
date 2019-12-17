$(document).ready(function() {
    $('#select_category').dropdown({
        allowCategorySelection: true,
      
        onChange: function(value, text, choice) { 
            var title = $(choice).attr('title');            
            $(this).children('.text').html(title);
            
            arikaim.page.loadContent({
                id: 'form_content',
                component: 'system:admin.orm.relations',
                params: { 
                    extension: 'category', 
                    model: 'CategoryRelations',                    
                    id: value 
                }
            },function(result) {
               
            });  
        }
    });
});