'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.form.addRules("#editor_form");   
    blogPostView.createEditor();

    arikaim.ui.button('.nlp-button',function(element) {
        $('#nlp_content').toggle(500);
        
        return arikaim.page.loadContent({
            id: 'nlp_content',           
            component: 'blog::admin.nlp',
            params: {}
        });  
    });
});