'use strict';

arikaim.component.onLoaded(function() {
    $('#languages_list').dropdown({
        onChange: function(value, text, $choice) {
            arikaim.ui.form.clearErrors('#language_form');

            $('#code').val(value);
            $('#code_3').val($($choice).attr('full-code'));
            $('#title').val($($choice).attr('data-title'));
            $('#native_title').val($($choice).attr('native-title'));
        }
    });  

    arikaim.ui.form.addRules('#language_form',{});

    arikaim.ui.form.onSubmit('#language_form',function() {         
        return arikaim.post('/core/api/language/add','#language_form');
    },function(result) {      
        arikaim.ui.form.showMessage(result.message);
        arikaim.ui.form.clear('#language_form');
    });
});