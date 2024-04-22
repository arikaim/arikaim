'use strict';

arikaim.component.onLoaded(function() {    
    $('.popup').popup();
    
    $('#language_select').dropdown({
        onChange: function(value) {               
            arikaim.setLanguage(value);
        }
    });
});