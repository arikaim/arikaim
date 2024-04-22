'use strict';

arikaim.component.onLoaded(function() {
    function loadEditTabs(uuid) {
        arikaim.page.loadContent({
            id: 'category_tabs_content',
            component: 'category::admin.edit.tabs',
            params: { 
                uuid: uuid           
            }
        });  
    }

    $('#select_category').dropdown({
        allowCategorySelection: true,
        onChange: function(value, text, choice) { 
            var title = $(choice).attr('title');
            
            $(this).children('.text').html(title);
           
            if (isEmpty(value) == true) {
                arikaim.ui.hide('#category_tabs_content');
            } else {
                arikaim.ui.show('#category_tabs_content');
            }
            loadEditTabs(value);
        }
    });
});