'use strict';

arikaim.component.onLoaded(function() {  
    arikaim.ui.button('.close-button',function(element) {
        $('#nlp_content').hide(400);
    });

    arikaim.ui.button('.generate-text-button',function(element) {
        var text = $('#text').val().trim();
        if (isEmpty(text) == true) {
            return false;
        }

        return arikaim.page.loadContent({
            id: 'text_generation_result',           
            component: 'blog::admin.nlp.generate',
            params: { text: text }
        });  
    });
});