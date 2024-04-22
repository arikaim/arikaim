'use strict';

arikaim.component.onLoaded(function() {
    $('#choose_language').dropdown({
        onChange: function(uuid) {         
            languages.load(uuid,function(result) {
                arikaim.ui.form.clearErrors('#language_form');
                initEditLanguageForm();
            });           
        }
    });    

    function initEditLanguageForm() {
        arikaim.ui.form.addRules('#language_form',{});

        arikaim.ui.form.onSubmit('#language_form',function() {      
            return arikaim.put('/core/api/language/update','#language_form');
        },function(result) {
            arikaim.ui.form.showMessage(result.message);            
        });
    }

    initEditLanguageForm();
});
