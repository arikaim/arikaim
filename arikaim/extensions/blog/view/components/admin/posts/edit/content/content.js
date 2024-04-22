'use strict';

arikaim.component.onLoaded(function() {
    $('#post_status').dropdown({
        onChange: function(value) {           
            var uuid = $(this).attr('uuid');         
            blogApi.setPostStatus(uuid,value);
        }       
    });

    arikaim.ui.form.onSubmit("#editor_form",function() {  
        return blogApi.updatePost('#editor_form',function(result) {
            blogPostView.updateItem(result.uuid);
        });
    },function(result) {          
        arikaim.ui.form.showMessage(result.message);        
    },function(error) {
        arikaim.ui.form.showErrors(error);        
    });
});