'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.form.addRules("#content_edit_form",{});
    
    arikaim.ui.form.onSubmit("#content_edit_form",function() {  
        return contentApi.update('#content_edit_form');
    },function(result) {       
        arikaim.ui.form.showMessage(result.message);
    }); 
});