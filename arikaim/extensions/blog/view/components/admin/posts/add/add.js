'use strict';

arikaim.component.onLoaded(function() {
    $('#post_status').dropdown({});

    arikaim.ui.form.onSubmit("#editor_form",function() {  
        return blogApi.addPost('#editor_form',function(result) {
            blogPostView.addItem({
                uuid: result.uuid
            });
            
            return arikaim.page.loadContent({
                id: 'details_content',           
                component: 'blog::admin.posts.edit',
                params: { 
                    uuid: result.uuid
                }
            });
        });
    },function(result) {          
        arikaim.ui.form.showMessage(result.message);        
    });
});
