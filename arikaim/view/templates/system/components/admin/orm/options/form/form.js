$(document).ready(function() {
    $('.option-dropdown').dropdown();
    $('.option-field').popup();
    
    arikaim.ui.form.onSubmit('#options_form',function() {       
        return arikaim.put('/core/api/orm/options','#options_form');
    },function(result) {      
        arikaim.ui.form.showMessage(result.message);
    });
});